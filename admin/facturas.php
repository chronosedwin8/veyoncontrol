<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$q = (string) input('q');
$estado = (string) input('estado');
$desde = (string) input('desde');
$hasta = (string) input('hasta');

$where = '1=1';
$params = [];
if ($q !== '') {
    $where .= ' AND (i.number LIKE ? OR c.institution LIKE ? OR c.email LIKE ?)';
    $like = '%' . addcslashes($q, '%_\\') . '%';
    array_push($params, $like, $like, $like);
}
switch ($estado) {
    case 'por_cobrar': $where .= " AND i.status IN ('pending','partial')"; break;
    case 'vencidas':   $where .= " AND i.status IN ('pending','partial') AND i.due_date < CURDATE()"; break;
    case 'pagadas':    $where .= " AND i.status = 'paid'"; break;
    case 'borrador':   $where .= " AND i.status = 'draft'"; break;
    case 'anuladas':   $where .= " AND i.status = 'cancelled'"; break;
}
if (valid_date($desde)) {
    $where .= ' AND i.issue_date >= ?';
    $params[] = $desde;
}
if (valid_date($hasta)) {
    $where .= ' AND i.issue_date <= ?';
    $params[] = $hasta;
}

$base = "FROM invoices i JOIN clients c ON c.id = i.client_id WHERE {$where}";

if (input('export') === 'csv') {
    $rows = db_all("SELECT i.number, i.issue_date, i.due_date, c.institution, c.tax_id, i.status, i.subtotal, i.discount, i.tax_amount, i.total, i.amount_paid, (i.total - i.amount_paid) balance {$base} ORDER BY i.issue_date DESC", $params);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="facturas-' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Número', 'Emisión', 'Vencimiento', 'Cliente', 'NIT', 'Estado', 'Subtotal', 'Descuento', 'Impuesto', 'Total', 'Pagado', 'Saldo'], ';');
    foreach ($rows as $r) {
        $r['status'] = invoice_status($r + ['due_date' => $r['due_date']])[0];
        fputcsv($out, $r, ';');
    }
    exit;
}

$sum = db_one("SELECT COALESCE(SUM(i.total),0) total, COALESCE(SUM(i.amount_paid),0) paid {$base} AND i.status <> 'cancelled'", $params);
$p = paginate((int) db_val("SELECT COUNT(*) {$base}", $params), 25);
$invoices = db_all("SELECT i.*, c.institution {$base} ORDER BY i.issue_date DESC, i.id DESC LIMIT {$p['limit']} OFFSET {$p['offset']}", $params);

$title = 'Facturas';
$area = 'admin';
$nav = 'facturas';
require APP_ROOT . '/app/views/panel_header.php';
$tabs = ['' => 'Todas', 'por_cobrar' => 'Por cobrar', 'vencidas' => 'Vencidas', 'pagadas' => 'Pagadas', 'borrador' => 'Borradores', 'anuladas' => 'Anuladas'];
?>
<div class="page-head">
  <div><h1>Facturas</h1><p>Facturado: <b><?= e(money($sum['total'])) ?></b> · Cobrado: <b><?= e(money($sum['paid'])) ?></b> · Pendiente: <b><?= e(money($sum['total'] - $sum['paid'])) ?></b></p></div>
  <div class="actions">
    <a class="btn" href="?<?= e(http_build_query(array_merge($_GET, ['export' => 'csv']))) ?>">Exportar CSV</a>
    <a class="btn btn-primary" href="<?= e(url('admin/factura_form.php')) ?>">Nueva factura</a>
  </div>
</div>
<div class="card">
  <nav class="tabs">
    <?php foreach ($tabs as $k => $label): ?>
      <a href="?<?= e(http_build_query(['estado' => $k, 'q' => $q, 'desde' => $desde, 'hasta' => $hasta])) ?>"<?= $estado === $k ? ' class="on"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <form class="filters" method="get">
    <input type="hidden" name="estado" value="<?= e($estado) ?>">
    <input class="input" type="search" name="q" value="<?= e($q) ?>" placeholder="Número, cliente o correo">
    <label class="small muted">Desde <input class="input" type="date" name="desde" value="<?= e($desde) ?>"></label>
    <label class="small muted">Hasta <input class="input" type="date" name="hasta" value="<?= e($hasta) ?>"></label>
    <button class="btn">Filtrar</button>
  </form>
  <?php if (!$invoices): ?>
    <div class="empty">No hay facturas con estos filtros.</div>
  <?php else: ?>
  <div class="table-wrap"><table class="tbl">
    <thead><tr><th>Número</th><th>Cliente</th><th>Emisión</th><th>Vence</th><th>Estado</th><th class="num">Total</th><th class="num">Saldo</th></tr></thead>
    <tbody>
    <?php foreach ($invoices as $inv): ?>
      <tr>
        <td><a class="row-link" href="<?= e(url('admin/factura.php?id=' . $inv['id'])) ?>"><?= e($inv['number']) ?></a></td>
        <td><a href="<?= e(url('admin/cliente.php?id=' . $inv['client_id'])) ?>"><?= e($inv['institution']) ?></a></td>
        <td class="nowrap"><?= e(fdate($inv['issue_date'])) ?></td>
        <td class="nowrap"><?= e(fdate($inv['due_date'])) ?></td>
        <td><?= badge(invoice_status($inv)) ?></td>
        <td class="num"><?= e(money($inv['total'])) ?></td>
        <td class="num"><?= in_array($inv['status'], ['pending', 'partial'], true) ? e(money(invoice_balance($inv))) : '<span class="muted">—</span>' ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?= pagination_links($p) ?>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
