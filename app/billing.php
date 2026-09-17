<?php
declare(strict_types=1);

/** Número consecutivo atómico (seguro ante peticiones concurrentes). */
function next_document_number(string $counter): string
{
    db_exec('INSERT IGNORE INTO counters (name, value) VALUES (?, 0)', [$counter]);
    db_exec('UPDATE counters SET value = LAST_INSERT_ID(value + 1) WHERE name = ?', [$counter]);
    $n = (int) db()->lastInsertId();
    $prefix = setting($counter === 'invoice' ? 'invoice_prefix' : 'quote_prefix', $counter === 'invoice' ? 'FV-' : 'COT-');
    return $prefix . str_pad((string) $n, 5, '0', STR_PAD_LEFT);
}

/** Lee las líneas del editor de documentos (arrays item_*[]). */
function items_from_request(): array
{
    $desc  = (array) ($_POST['item_desc'] ?? []);
    $qty   = (array) ($_POST['item_qty'] ?? []);
    $price = (array) ($_POST['item_price'] ?? []);
    $plan  = (array) ($_POST['item_plan'] ?? []);
    $validPlans = array_map('intval', array_column(db_all('SELECT id FROM plans'), 'id'));

    $items = [];
    foreach ($desc as $i => $d) {
        $d = trim((string) $d);
        if ($d === '') {
            continue;
        }
        $q = max(0.01, parse_money($qty[$i] ?? 1));
        $p = max(0.0, parse_money($price[$i] ?? 0));
        $planId = (int) ($plan[$i] ?? 0);
        $items[] = [
            'plan_id'     => in_array($planId, $validPlans, true) ? $planId : null,
            'description' => mb_substr($d, 0, 255),
            'quantity'    => $q,
            'unit_price'  => $p,
            'line_total'  => round($q * $p, 2),
        ];
    }
    return $items;
}

function compute_totals(array $items, float $discount, float $taxRate): array
{
    $subtotal = round(array_sum(array_column($items, 'line_total')), 2);
    $discount = min(max(0.0, $discount), $subtotal);
    $taxRate = min(max(0.0, $taxRate), 100.0);
    $tax = round(($subtotal - $discount) * $taxRate / 100, 2);
    return [
        'subtotal'   => $subtotal,
        'discount'   => $discount,
        'tax_rate'   => $taxRate,
        'tax_amount' => $tax,
        'total'      => round($subtotal - $discount + $tax, 2),
    ];
}

/**
 * Crea o actualiza una factura o cotización con sus líneas.
 * $type: 'invoice' | 'quote'
 */
function save_document(string $type, ?int $id, array $header, array $items): int
{
    $table = $type === 'invoice' ? 'invoices' : 'quotes';
    $itemsTable = $type === 'invoice' ? 'invoice_items' : 'quote_items';
    $fk = $type === 'invoice' ? 'invoice_id' : 'quote_id';

    return db_transaction(function () use ($type, $id, $header, $items, $table, $itemsTable, $fk) {
        $header = array_merge($header, compute_totals($items, (float) ($header['discount'] ?? 0), (float) ($header['tax_rate'] ?? 0)));
        if ($id) {
            db_update($table, $header, 'id = ?', [$id]);
            db_exec("DELETE FROM {$itemsTable} WHERE {$fk} = ?", [$id]);
        } else {
            $header['number'] = next_document_number($type);
            $id = db_insert($table, $header);
        }
        foreach (array_values($items) as $i => $item) {
            db_insert($itemsTable, $item + [$fk => $id, 'sort_order' => $i]);
        }
        if ($type === 'invoice') {
            recalc_invoice($id);
        }
        return $id;
    });
}

function document_items(string $type, int $id): array
{
    return $type === 'invoice'
        ? db_all('SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY sort_order, id', [$id])
        : db_all('SELECT * FROM quote_items WHERE quote_id = ? ORDER BY sort_order, id', [$id]);
}

function invoice_balance(array $invoice): float
{
    return max(0.0, round((float) $invoice['total'] - (float) $invoice['amount_paid'], 2));
}

/**
 * Recalcula lo pagado y el estado de la factura a partir de los pagos aprobados.
 * Si la factura queda pagada por completo, activa las licencias de sus planes.
 */
function recalc_invoice(int $invoiceId): void
{
    $inv = db_one('SELECT * FROM invoices WHERE id = ? FOR UPDATE', [$invoiceId]);
    if (!$inv) {
        return;
    }
    $paid = (float) db_val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE invoice_id = ? AND status = 'approved'", [$invoiceId]);
    $data = ['amount_paid' => round($paid, 2)];

    if (!in_array($inv['status'], ['draft', 'cancelled'], true)) {
        if ($paid + 0.009 >= (float) $inv['total'] && (float) $inv['total'] > 0) {
            $data['status'] = 'paid';
            $data['paid_at'] = $inv['paid_at'] ?: date('Y-m-d H:i:s');
        } elseif ($paid > 0) {
            $data['status'] = 'partial';
            $data['paid_at'] = null;
        } else {
            $data['status'] = 'pending';
            $data['paid_at'] = null;
        }
    }
    db_update('invoices', $data, 'id = ?', [$invoiceId]);

    if (($data['status'] ?? $inv['status']) === 'paid' && $inv['status'] !== 'paid') {
        activate_licenses($invoiceId);
        log_activity('invoice_paid', 'invoice', $invoiceId, $inv['number']);
    }
}

/** Crea una licencia por cada línea de plan de una factura pagada (idempotente). */
function activate_licenses(int $invoiceId): void
{
    $inv = db_one('SELECT * FROM invoices WHERE id = ?', [$invoiceId]);
    if (!$inv) {
        return;
    }
    $months = max(1, (int) setting('license_months', '12'));
    $items = db_all(
        'SELECT ii.*, p.name AS plan_name, p.capacity_label FROM invoice_items ii JOIN plans p ON p.id = ii.plan_id WHERE ii.invoice_id = ?',
        [$invoiceId]
    );
    foreach ($items as $item) {
        if (db_val('SELECT id FROM licenses WHERE invoice_item_id = ?', [$item['id']])) {
            continue;
        }
        // Renovación: si hay una licencia vigente del mismo plan, se encadena al vencimiento
        $lastEnd = db_val(
            "SELECT MAX(end_date) FROM licenses WHERE client_id = ? AND plan_id = ? AND status = 'active' AND end_date >= CURDATE()",
            [$inv['client_id'], $item['plan_id']]
        );
        $start = $lastEnd ? new DateTime($lastEnd . ' +1 day') : new DateTime('today');
        $end = (clone $start)->modify("+{$months} months -1 day");
        db_insert('licenses', [
            'client_id'       => $inv['client_id'],
            'plan_id'         => $item['plan_id'],
            'invoice_id'      => $invoiceId,
            'invoice_item_id' => $item['id'],
            'description'     => $item['plan_name'] . ($item['capacity_label'] ? ' · ' . $item['capacity_label'] : ''),
            'start_date'      => $start->format('Y-m-d'),
            'end_date'        => $end->format('Y-m-d'),
        ]);
    }
}

/** Convierte una cotización en factura pendiente. Devuelve el id de la factura. */
function convert_quote_to_invoice(int $quoteId, ?int $adminId = null): int
{
    $quote = db_one('SELECT * FROM quotes WHERE id = ?', [$quoteId]);
    if (!$quote) {
        throw new RuntimeException('Cotización no encontrada');
    }
    if ($quote['invoice_id']) {
        return (int) $quote['invoice_id'];
    }
    $items = array_map(fn ($i) => [
        'plan_id'     => $i['plan_id'],
        'description' => $i['description'],
        'quantity'    => $i['quantity'],
        'unit_price'  => $i['unit_price'],
        'line_total'  => $i['line_total'],
    ], document_items('quote', $quoteId));

    $invoiceId = save_document('invoice', null, [
        'client_id'  => $quote['client_id'],
        'quote_id'   => $quoteId,
        'status'     => 'pending',
        'issue_date' => date('Y-m-d'),
        'due_date'   => date('Y-m-d', strtotime('+' . (int) setting('invoice_due_days', '15') . ' days')),
        'discount'   => $quote['discount'],
        'tax_rate'   => $quote['tax_rate'],
        'notes'      => $quote['notes'],
        'created_by' => $adminId,
    ], $items);

    db_update('quotes', ['status' => 'invoiced', 'invoice_id' => $invoiceId], 'id = ?', [$quoteId]);
    $number = db_val('SELECT number FROM invoices WHERE id = ?', [$invoiceId]);
    log_activity('quote_invoiced', 'quote', $quoteId, "{$quote['number']} → {$number}");
    return $invoiceId;
}

/** Factura de compra en línea de un plan desde el sitio público. */
function create_plan_invoice(int $clientId, array $plan): int
{
    // Reutiliza una factura pendiente idéntica sin pagos para no duplicar
    $existing = db_val(
        "SELECT i.id FROM invoices i JOIN invoice_items ii ON ii.invoice_id = i.id
         WHERE i.client_id = ? AND i.status = 'pending' AND i.amount_paid = 0 AND ii.plan_id = ? AND ii.unit_price = ?
           AND i.created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
           AND (SELECT COUNT(*) FROM invoice_items x WHERE x.invoice_id = i.id) = 1
         ORDER BY i.id DESC LIMIT 1",
        [$clientId, $plan['id'], $plan['price_cop']]
    );
    if ($existing) {
        return (int) $existing;
    }
    $id = save_document('invoice', null, [
        'client_id'  => $clientId,
        'status'     => 'pending',
        'issue_date' => date('Y-m-d'),
        'due_date'   => date('Y-m-d', strtotime('+' . (int) setting('invoice_due_days', '15') . ' days')),
        'discount'   => 0,
        'tax_rate'   => (float) setting('tax_rate', '0'),
        'notes'      => 'Compra en línea desde el sitio web.',
    ], [[
        'plan_id'     => (int) $plan['id'],
        'description' => $plan['name'] . ' (' . $plan['capacity_label'] . ') — licencia anual con actualizaciones, capacitación y soporte',
        'quantity'    => 1,
        'unit_price'  => (float) $plan['price_cop'],
        'line_total'  => (float) $plan['price_cop'],
    ]]);
    log_activity('invoice_created_online', 'invoice', $id, $plan['name']);
    return $id;
}

/** Registra un pago manual y recalcula la factura. */
function record_manual_payment(array $data): int
{
    return db_transaction(function () use ($data) {
        $id = db_insert('payments', $data);
        if (!empty($data['invoice_id'])) {
            recalc_invoice((int) $data['invoice_id']);
        }
        return $id;
    });
}

function plans_catalog(bool $onlyActive = false): array
{
    return db_all('SELECT * FROM plans' . ($onlyActive ? ' WHERE active = 1' : '') . ' ORDER BY sort_order, id');
}
