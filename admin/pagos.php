<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$q = (string) input('q');
$metodo = (string) input('metodo');
$estado = (string) input('estado');
$desde = (string) input('desde');
$hasta = (string) input('hasta');

$where = '1=1';
$params = [];
if ($q !== '') {
    $where .= ' AND (c.institution LIKE ? OR i.number LIKE ? OR p.reference LIKE ? OR p.mp_payment_id LIKE ?)';
    $like = '%' . addcslashes($q, '%_\\') . '%';
    array_push($params, $like, $like, $like, $like);
}
if (isset(payment_methods()[$metodo])) {
    $where .= ' AND p.method = ?';
    $params[] = $metodo;
}
if (in_array($estado, ['approved', 'pending', 'in_process', 'rejected', 'cancelled', 'refunded', 'charged_back'], true)) {
    $where .= ' AND p.status = ?';
    $params[] = $estado;
}
if (valid_date($desde)) {
    $where .= ' AND COALESCE(p.paid_at, p.created_at) >= ?';
    $params[] = $desde . ' 00:00:00';
}
if (valid_date($hasta)) {
    $where .= ' AND COALESCE(p.paid_at, p.created_at) <= ?';
    $params[] = $hasta . ' 23:59:59';
}
$base = "FROM payments p JOIN clients c ON c.id = p.client_id LEFT JOIN invoices i ON i.id = p.invoice_id WHERE {$where}";

if (input('export') === 'csv') {
    $rows = db_all("SELECT COALESCE(p.paid_at, p.created_at) fecha, c.institution, c.tax_id, i.number, p.method, p.status, p.amount, p.reference, p.mp_payment_id, p.mp_payment_type {$base} ORDER BY fecha DESC", $params);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="pagos-' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Fecha', 'Cliente', 'NIT', 'Factura', 'Medio', 'Estado', 'Valor', 'Referencia', 'ID Mercado Pago', 'Tipo MP'], ';');
    foreach ($rows as $r) {
        $r['method'] = payment_methods()[$r['method']] ?? $r['method'];
        $r['status'] = payment_status($r['status'])[0];
        fputcsv($out, $r, ';');
    }
    exit;
}

$sum = (float) db_val("SELECT COALESCE(SUM(p.amount),0) {$base} AND p.status = 'approved'", $params);
$p = paginate((int) db_val("SELECT COUNT(*) {$base}", $params), 30);
$payments = db_all("SELECT p.*, c.institution, i.number {$base} ORDER BY COALESCE(p.paid_at, p.created_at) DESC, p.id DESC LIMIT {$p['limit']} OFFSET {$p['offset']}", $params);

$title = 'Pagos';
$area = 'admin';
$nav = 'pagos';
require APP_ROOT . '/app/views/panel_header.php';
$statuses = ['approved', 'pending', 'in_process', 'rejected', 'cancelled', 'refunded', 'charged_back'];
?>
<div class="page-head">
  <div><h1>Pagos</h1><p>Total aprobado con estos filtros: <b><?= e(money($sum, true)) ?></b></p></div>
  <div class="actions">
    <a class="btn" href="?<?= e(http_build_query(array_merge($_GET, ['export' => 'csv']))) ?>">Exportar CSV</a>
    <a class="btn btn-primary" href="<?= e(url('admin/pago_form.php')) ?>">Registrar pago</a>
  </div>
</div>
<div class="card">
  <form class="filters" method="get">
    <input class="input" type="search" name="q" value="<?= e($q) ?>" placeholder="Cliente, factura o referencia">
    <select class="input" name="metodo" data-autosubmit><option value="">Todos los medios</option><?php foreach (payment_methods() as $k => $label): ?><option value="<?= e($k) ?>"<?= $metodo === $k ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select>
    <select class="input" name="estado" data-autosubmit><option value="">Todos los estados</option><?php foreach ($statuses as $s): ?><option value="<?= e($s) ?>"<?= $estado === $s ? ' selected' : '' ?>><?= e(payment_status($s)[0]) ?></option><?php endforeach; ?></select>
    <label class="small muted">Desde <input class="input" type="date" name="desde" value="<?= e($desde) ?>"></label>
    <label class="small muted">Hasta <input class="input" type="date" name="hasta" value="<?= e($hasta) ?>"></label>
    <button class="btn">Filtrar</button>
  </form>
  <?php if (!$payments): ?>
    <div class="empty">No hay pagos con estos filtros.</div>
  <?php else: ?>
  <div class="table-wrap"><table class="tbl">
    <thead><tr><th>Fecha</th><th>Cliente</th><th>Factura</th><th>Medio</th><th>Referencia</th><th>Estado</th><th class="num">Valor</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($payments as $pay): ?>
      <tr>
        <td class="nowrap"><?= e(fdate($pay['paid_at'] ?: $pay['created_at'], true)) ?></td>
        <td><a class="row-link" href="<?= e(url('admin/cliente.php?id=' . $pay['client_id'] . '&tab=pagos')) ?>"><?= e($pay['institution']) ?></a></td>
        <td><?= $pay['invoice_id'] ? '<a href="' . e(url('admin/factura.php?id=' . $pay['invoice_id'])) . '">' . e($pay['number']) . '</a>' : '<span class="muted">—</span>' ?></td>
        <td><?= e(payment_methods()[$pay['method']] ?? $pay['method']) ?><?= $pay['mp_payment_type'] ? '<br><span class="small muted">' . e($pay['mp_payment_type']) . '</span>' : '' ?></td>
        <td class="mono small"><?= e($pay['mp_payment_id'] ?: $pay['reference']) ?></td>
        <td><?= badge(payment_status($pay['status'])) ?></td>
        <td class="num"><?= e(money($pay['amount'])) ?></td>
        <td class="right"><?php if ($pay['method'] !== 'mercadopago'): ?><a class="btn btn-sm" href="<?= e(url('admin/pago_form.php?id=' . $pay['id'])) ?>">Editar</a><?php endif; ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?= pagination_links($p) ?>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
