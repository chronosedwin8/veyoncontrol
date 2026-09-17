<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$admin = require_admin();

$id = input_int('id');
$doc = $id ? db_one('SELECT * FROM quotes WHERE id = ?', [$id]) : null;
if ($id && !$doc) {
    flash('error', 'Cotización no encontrada.');
    redirect('admin/cotizaciones.php');
}
if ($doc && in_array($doc['status'], ['invoiced', 'accepted'], true)) {
    flash('warn', 'Las cotizaciones aceptadas o facturadas no se pueden editar. Duplícala para crear una nueva versión.');
    redirect('admin/cotizacion.php?id=' . $id);
}

$v = $doc ?: [
    'client_id'   => input_int('client_id'),
    'status'      => 'sent',
    'issue_date'  => date('Y-m-d'),
    'valid_until' => date('Y-m-d', strtotime('+' . (int) setting('quote_valid_days', '30') . ' days')),
    'discount'    => 0,
    'tax_rate'    => setting('tax_rate', '0'),
    'notes'       => '',
];
$items = $doc ? document_items('quote', $id) : [];

// Duplicar cotización existente
if (!$doc && input_int('from')) {
    $src = db_one('SELECT * FROM quotes WHERE id = ?', [input_int('from')]);
    if ($src) {
        $v = array_merge($v, array_intersect_key($src, array_flip(['client_id', 'discount', 'tax_rate', 'notes'])));
        $items = document_items('quote', (int) $src['id']);
    }
}
$errors = [];

if (is_post()) {
    verify_csrf();
    $v = [
        'client_id'   => input_int('client_id'),
        'status'      => input('status') === 'draft' ? 'draft' : 'sent',
        'issue_date'  => (string) input('issue_date'),
        'valid_until' => (string) input('valid_until'),
        'discount'    => parse_money(input('discount')),
        'tax_rate'    => parse_money(input('tax_rate')),
        'notes'       => mb_substr((string) input('notes'), 0, 5000),
    ];
    $items = items_from_request();

    if (!db_val('SELECT id FROM clients WHERE id = ?', [$v['client_id']])) {
        $errors[] = 'Selecciona un cliente válido.';
    }
    if (!valid_date($v['issue_date'])) {
        $errors[] = 'La fecha de emisión no es válida.';
    }
    if ($v['valid_until'] !== '' && !valid_date($v['valid_until'])) {
        $errors[] = 'La fecha de validez no es válida.';
    }
    if (!$items) {
        $errors[] = 'Agrega al menos un concepto.';
    }
    if (!$errors) {
        $header = $v;
        $header['valid_until'] = $v['valid_until'] ?: null;
        if (!$doc) {
            $header['created_by'] = $admin['id'];
        }
        $newId = save_document('quote', $doc ? $id : null, $header, $items);
        log_activity($doc ? 'quote_updated' : 'quote_created', 'quote', $newId);
        flash('ok', $doc ? 'Cotización actualizada.' : 'Cotización creada.');
        redirect('admin/cotizacion.php?id=' . $newId);
    }
}

$docType = 'quote';
$editing = (bool) $doc;
$clients = db_all("SELECT id, institution, email FROM clients WHERE status = 'active' OR id = ? ORDER BY institution", [(int) $v['client_id']]);
$plans = plans_catalog();

$title = $doc ? 'Editar cotización ' . $doc['number'] : 'Nueva cotización';
$area = 'admin';
$nav = 'cotizaciones';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head">
  <div><div class="crumbs"><a href="<?= e(url('admin/cotizaciones.php')) ?>">Cotizaciones</a></div><h1><?= e($title) ?></h1></div>
</div>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
<?php require APP_ROOT . '/app/views/doc_form.php'; ?>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
