<?php
/**
 * Webhook de Mercado Pago.
 * Configurar en Mercado Pago > Tus integraciones > Webhooks:
 *   URL:    https://TU-DOMINIO/api/mp_webhook.php
 *   Evento: Pagos
 */
require dirname(__DIR__) . '/app/bootstrap.php';

header('Content-Type: application/json');

if (!is_post()) {
    http_response_code(405);
    echo json_encode(['error' => 'method_not_allowed']);
    exit;
}

$raw = file_get_contents('php://input') ?: '';
$body = json_decode($raw, true) ?: [];

$topic = (string) ($body['type'] ?? $body['topic'] ?? $_GET['type'] ?? $_GET['topic'] ?? '');
$dataId = (string) ($body['data']['id'] ?? $_GET['data_id'] ?? $_GET['id'] ?? '');
$dataId = preg_replace('/[^\w\-]/', '', $dataId);

$signatureOk = mp_verify_signature((string) ($_GET['data_id'] ?? $dataId));

$eventId = db_insert('webhook_events', [
    'provider'     => 'mercadopago',
    'topic'        => mb_substr($topic, 0, 40),
    'resource_id'  => mb_substr($dataId, 0, 60),
    'request_id'   => mb_substr((string) ($_SERVER['HTTP_X_REQUEST_ID'] ?? ''), 0, 80),
    'signature_ok' => $signatureOk === null ? null : (int) $signatureOk,
    'payload'      => mb_substr($raw, 0, 60000),
]);

if ($signatureOk === false) {
    db_update('webhook_events', ['error' => 'firma inválida'], 'id = ?', [$eventId]);
    http_response_code(401);
    echo json_encode(['error' => 'invalid_signature']);
    exit;
}

if ($topic !== 'payment' || $dataId === '') {
    db_update('webhook_events', ['processed' => 1, 'error' => 'ignorado'], 'id = ?', [$eventId]);
    echo json_encode(['ok' => true, 'ignored' => true]);
    exit;
}

try {
    // Nunca se confía en el contenido de la notificación: se consulta el pago a la API
    $payment = mp_get_payment($dataId);
    if (!$payment) {
        // Pago inexistente (p. ej. «Simular notificación» del panel): no se reintenta
        db_update('webhook_events', ['processed' => 1, 'error' => 'pago no encontrado en Mercado Pago'], 'id = ?', [$eventId]);
        echo json_encode(['ok' => true, 'ignored' => true]);
        exit;
    }
    $invoiceId = mp_apply_payment($payment);
    db_update('webhook_events', ['processed' => 1, 'error' => $invoiceId ? null : 'sin factura asociada'], 'id = ?', [$eventId]);
    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    error_log('MP webhook: ' . $e->getMessage());
    db_update('webhook_events', ['error' => mb_substr($e->getMessage(), 0, 255)], 'id = ?', [$eventId]);
    // 500 => Mercado Pago reintentará la notificación
    http_response_code(500);
    echo json_encode(['error' => 'processing_failed']);
}
