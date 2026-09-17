<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$admin = require_admin();

$id = input_int('id');
$doc = $id ? db_one('SELECT * FROM invoices WHERE id = ?', [$id]) : null;
if ($id && !$doc) {
    flash('error', 'Factura no encontrada.');
    redirect('admin/facturas.php');
}
if ($doc && ((float) $doc['amount_paid'] > 0 || in_array($doc['status'], ['paid', 'cancelled'], true))) {
    flash('warn', 'Las facturas con pagos, pagadas o anuladas no se pueden editar.');
    redirect('admin/factura.php?id=' . $id);
}

$v = $doc ?: [
    'client_id'  => input_int('client_id'),
    'status'     => 'pending',
    'issue_date' => date('Y-m-d'),
    'due_date'   => date('Y-m-d', strtotime('+' . (int) setting('invoice_due_days', '15') . ' days')),
    'discount'   => 0,
    'tax_rate'   => setting('tax_rate', '0'),
    'notes'      => '',
];
$items = $doc ? document_items('invoice', $id) : [];
$errors = [];

if (is_post()) {
    verify_csrf();
    $v = [
        'client_id'  => input_int('client_id'),
        'status'     => input('status') === 'draft' ? 'draft' : 'pending',
        'issue_date' => (string) input('issue_date'),
        'due_date'   => (string) input('due_date'),
        'discount'   => parse_money(input('discount')),
        'tax_rate'   => parse_money(input('tax_rate')),
        'notes'      => mb_substr((string) input('notes'), 0, 5000),
    ];
    $items = items_from_request();

    if (!db_val("SELECT id FROM clients WHERE id = ?", [$v['client_id']])) {
        $errors[] = 'Selecciona un cliente válido.';
    }
    if (!valid_date($v['issue_date'])) {
        $errors[] = 'La fecha de emisión no es válida.';
    }
    if ($v['due_date'] !== '' && !valid_date($v['due_date'])) {
        $errors[] = 'La fecha de vencimiento no es válida.';
    }
    if (!$items) {
        $errors[] = 'Agrega al menos un concepto.';
    }
    if (!$errors) {
        $header = $v;
        $header['due_date'] = $v['due_date'] ?: null;
        if (!$doc) {
            $header['created_by'] = $admin['id'];
        }
        $newId = save_document('invoice', $doc ? $id : null, $header, $items);
        log_activity($doc ? 'invoice_updated' : 'invoice_created', 'invoice', $newId);
        flash('ok', $doc ? 'Factura actualizada.' : 'Factura creada.');
        redirect('admin/factura.php?id=' . $newId);
    }
}

$docType = 'invoice';
$editing = (bool) $doc;
$clients = db_all("SELECT id, institution, email FROM clients WHERE status = 'active' OR id = ? ORDER BY institution", [(int) $v['client_id']]);
$plans = plans_catalog();

$title = $doc ? 'Editar factura ' . $doc['number'] : 'Nueva factura';
$area = 'admin';
$nav = 'facturas';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head">
  <div><div class="crumbs"><a href="<?= e(url('admin/facturas.php')) ?>">Facturas</a></div><h1><?= e($title) ?></h1></div>
</div>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
<?php require APP_ROOT . '/app/views/doc_form.php'; ?>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
