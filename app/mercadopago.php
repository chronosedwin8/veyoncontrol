<?php
declare(strict_types=1);

/**
 * Integración con Mercado Pago (Checkout Pro) mediante la API REST.
 * Sin SDK ni Composer: cURL + JSON.
 *
 * Flujo:
 *  1. mp_create_preference() crea la preferencia de la factura y devuelve la URL de pago.
 *  2. El cliente paga en Mercado Pago y vuelve a portal/pago_resultado.php
 *     (se consulta el pago y se aplica de inmediato).
 *  3. Mercado Pago notifica a api/mp_webhook.php (fuente de verdad, con firma verificada).
 *  4. mp_apply_payment() guarda/actualiza el pago (idempotente por mp_payment_id)
 *     y recalc_invoice() actualiza la factura y activa licencias.
 */

const MP_API = 'https://api.mercadopago.com';

function mp_configured(): bool
{
    return trim((string) config('mercadopago.access_token')) !== '';
}

function mp_is_test(): bool
{
    return str_starts_with(trim((string) config('mercadopago.access_token')), 'TEST-');
}

/** @return array{0:int,1:array} [httpStatus, body] */
function mp_request(string $method, string $path, ?array $body = null, ?string $idempotencyKey = null): array
{
    if (!mp_configured()) {
        throw new RuntimeException('Mercado Pago no está configurado (falta access_token en config/config.local.php).');
    }
    $headers = [
        'Authorization: Bearer ' . trim((string) config('mercadopago.access_token')),
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    if ($idempotencyKey) {
        $headers[] = 'X-Idempotency-Key: ' . $idempotencyKey;
    }
    $ch = curl_init(MP_API . $path);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 25,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($raw === false) {
        throw new RuntimeException('No se pudo conectar con Mercado Pago: ' . $err);
    }
    $json = json_decode((string) $raw, true);
    return [$status, is_array($json) ? $json : ['raw' => $raw]];
}

/** Crea la preferencia de pago para el saldo pendiente de una factura. Devuelve la URL de checkout. */
function mp_create_preference(array $invoice, array $client): string
{
    $balance = invoice_balance($invoice);
    if ($balance <= 0) {
        throw new RuntimeException('La factura no tiene saldo pendiente.');
    }

    $base = app_url();
    $isPublic = str_starts_with($base, 'https://') && !is_local_url($base);
    $company = setting('company_name', 'Veyon Control');

    $pref = [
        'items' => [[
            'id'          => 'INV-' . $invoice['id'],
            'title'       => mb_substr("{$company} · Factura {$invoice['number']}", 0, 250),
            'description' => mb_substr('Pago de la factura ' . $invoice['number'], 0, 250),
            'category_id' => 'services',
            'quantity'    => 1,
            'currency_id' => 'COP',
            // COP no admite decimales en Mercado Pago
            'unit_price'  => (float) ceil($balance),
        ]],
        'payer' => [
            'name'  => (string) ($client['contact_name'] ?: $client['institution']),
            'email' => (string) $client['email'],
        ],
        'external_reference'   => 'INV-' . $invoice['id'],
        'statement_descriptor' => mb_substr((string) config('mercadopago.statement_descriptor', 'VEYONCONTROL'), 0, 22),
        'back_urls' => [
            'success' => $base . '/portal/pago_resultado.php?inv=' . $invoice['id'],
            'pending' => $base . '/portal/pago_resultado.php?inv=' . $invoice['id'],
            'failure' => $base . '/portal/pago_resultado.php?inv=' . $invoice['id'],
        ],
        'metadata' => ['invoice_id' => (int) $invoice['id'], 'client_id' => (int) $client['id']],
        'expires'            => true,
        'expiration_date_to' => date('Y-m-d\TH:i:s.000P', strtotime('+3 days')),
    ];
    if ($isPublic) {
        // Mercado Pago rechaza auto_return y notification_url con URLs locales
        $pref['auto_return'] = 'approved';
        $pref['notification_url'] = $base . '/api/mp_webhook.php?source_news=webhooks';
    }

    [$status, $res] = mp_request('POST', '/checkout/preferences', $pref, 'pref-inv' . $invoice['id'] . '-' . bin2hex(random_bytes(8)));
    if ($status >= 300 || empty($res['id'])) {
        error_log('MP preference error: ' . json_encode($res));
        throw new RuntimeException('Mercado Pago rechazó la solicitud: ' . ($res['message'] ?? 'error ' . $status));
    }

    db_update('invoices', ['mp_preference_id' => $res['id']], 'id = ?', [$invoice['id']]);
    log_activity('mp_preference_created', 'invoice', (int) $invoice['id'], $res['id']);

    return (mp_is_test() && !empty($res['sandbox_init_point'])) ? $res['sandbox_init_point'] : $res['init_point'];
}

function mp_get_payment(string $paymentId): ?array
{
    if (!preg_match('/^\d{1,20}$/', $paymentId)) {
        return null;
    }
    [$status, $res] = mp_request('GET', '/v1/payments/' . $paymentId);
    return $status === 200 ? $res : null;
}

/**
 * Guarda o actualiza un pago de Mercado Pago y recalcula la factura.
 * Idempotente: puede llamarse varias veces con el mismo pago.
 * Devuelve el id de factura asociado o null.
 */
function mp_apply_payment(array $p): ?int
{
    $ref = (string) ($p['external_reference'] ?? '');
    if (!preg_match('/^INV-(\d+)$/', $ref, $m)) {
        return null;
    }
    $invoiceId = (int) $m[1];
    $invoice = db_one('SELECT * FROM invoices WHERE id = ?', [$invoiceId]);
    if (!$invoice) {
        return null;
    }

    $allowed = ['pending', 'in_process', 'approved', 'rejected', 'cancelled', 'refunded', 'charged_back'];
    $status = in_array($p['status'] ?? '', $allowed, true) ? $p['status'] : 'pending';
    if (($p['status'] ?? '') === 'authorized' || ($p['status'] ?? '') === 'in_mediation') {
        $status = 'in_process';
    }
    if (($p['currency_id'] ?? 'COP') !== 'COP') {
        error_log('MP pago con moneda inesperada: ' . ($p['id'] ?? '?'));
        $status = 'rejected';
    }

    $amount = (float) ($p['transaction_amount'] ?? 0);
    // Si se reembolsó parcialmente, solo cuenta lo no reembolsado
    if ($status === 'approved' && !empty($p['transaction_amount_refunded'])) {
        $amount = max(0.0, $amount - (float) $p['transaction_amount_refunded']);
    }

    $data = [
        'client_id'        => $invoice['client_id'],
        'invoice_id'       => $invoiceId,
        'method'           => 'mercadopago',
        'amount'           => $amount,
        'currency'         => 'COP',
        'status'           => $status,
        'reference'        => mb_substr((string) ($p['authorization_code'] ?? $p['id']), 0, 120),
        'mp_payment_id'    => (string) $p['id'],
        'mp_status_detail' => mb_substr((string) ($p['status_detail'] ?? ''), 0, 80),
        'mp_payment_type'  => mb_substr(trim(($p['payment_type_id'] ?? '') . ' ' . ($p['payment_method_id'] ?? '')), 0, 40),
        'paid_at'          => !empty($p['date_approved']) ? date('Y-m-d H:i:s', strtotime($p['date_approved'])) : null,
        'raw_response'     => json_encode($p, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ];

    db_transaction(function () use ($data, $invoiceId) {
        $existing = db_one('SELECT id, status FROM payments WHERE mp_payment_id = ? FOR UPDATE', [$data['mp_payment_id']]);
        if ($existing) {
            db_update('payments', $data, 'id = ?', [$existing['id']]);
            if ($existing['status'] !== $data['status']) {
                log_activity('mp_payment_status', 'payment', (int) $existing['id'], "{$existing['status']} → {$data['status']}");
            }
        } else {
            $pid = db_insert('payments', $data);
            log_activity('mp_payment_received', 'payment', $pid, "{$data['status']} {$data['amount']}");
        }
        recalc_invoice($invoiceId);
    });

    return $invoiceId;
}

/** Consulta en Mercado Pago todos los pagos de una factura y los aplica. Devuelve cuántos encontró. */
function mp_sync_invoice(int $invoiceId): int
{
    [$status, $res] = mp_request('GET', '/v1/payments/search?' . http_build_query([
        'external_reference' => 'INV-' . $invoiceId,
        'sort'               => 'date_created',
        'criteria'           => 'desc',
        'limit'              => 50,
    ]));
    if ($status !== 200) {
        throw new RuntimeException('No se pudo consultar Mercado Pago (HTTP ' . $status . ').');
    }
    $count = 0;
    foreach ($res['results'] ?? [] as $payment) {
        if (mp_apply_payment($payment) !== null) {
            $count++;
        }
    }
    return $count;
}

/**
 * Verifica la cabecera x-signature de los webhooks.
 * Plantilla oficial: "id:{data.id};request-id:{x-request-id};ts:{ts};"
 */
function mp_verify_signature(string $dataId): ?bool
{
    $secret = trim((string) config('mercadopago.webhook_secret'));
    if ($secret === '') {
        return null; // sin clave configurada no se puede verificar
    }
    $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
    $requestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';
    $parts = [];
    foreach (explode(',', $signature) as $chunk) {
        $kv = explode('=', trim($chunk), 2);
        if (count($kv) === 2) {
            $parts[trim($kv[0])] = trim($kv[1]);
        }
    }
    if (empty($parts['ts']) || empty($parts['v1'])) {
        return false;
    }
    $manifest = '';
    if ($dataId !== '') {
        $manifest .= 'id:' . (ctype_alnum($dataId) ? strtolower($dataId) : $dataId) . ';';
    }
    if ($requestId !== '') {
        $manifest .= 'request-id:' . $requestId . ';';
    }
    $manifest .= 'ts:' . $parts['ts'] . ';';
    return hash_equals(hash_hmac('sha256', $manifest, $secret), $parts['v1']);
}
