<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$q = (string) input('q');
$estado = (string) input('estado');
$where = '1=1';
$params = [];
if ($q !== '') {
    $where .= ' AND (x.number LIKE ? OR c.institution LIKE ? OR c.email LIKE ?)';
    $like = '%' . addcslashes($q, '%_\\') . '%';
    array_push($params, $like, $like, $like);
}
if ($estado === 'abiertas') {
    $where .= " AND x.status = 'sent' AND (x.valid_until IS NULL OR x.valid_until >= CURDATE())";
} elseif (in_array($estado, ['draft', 'accepted', 'rejected', 'invoiced'], true)) {
    $where .= ' AND x.status = ?';
    $params[] = $estado;
}
$base = "FROM quotes x JOIN clients c ON c.id = x.client_id WHERE {$where}";
$p = paginate((int) db_val("SELECT COUNT(*) {$base}", $params), 25);
$quotes = db_all("SELECT x.*, c.institution {$base} ORDER BY x.issue_date DESC, x.id DESC LIMIT {$p['limit']} OFFSET {$p['offset']}", $params);

$title = 'Cotizaciones';
$area = 'admin';
$nav = 'cotizaciones';
require APP_ROOT . '/app/views/panel_header.php';
$tabs = ['' => 'Todas', 'abiertas' => 'Abiertas', 'draft' => 'Borradores', 'accepted' => 'Aceptadas', 'invoiced' => 'Facturadas', 'rejected' => 'Rechazadas'];
?>
<div class="page-head">
  <div><h1>Cotizaciones</h1><p><?= (int) $p['total'] ?> cotización(es)</p></div>
  <div class="actions"><a class="btn btn-primary" href="<?= e(url('admin/cotizacion_form.php')) ?>">Nueva cotización</a></div>
</div>
<div class="card">
  <nav class="tabs">
    <?php foreach ($tabs as $k => $label): ?>
      <a href="?<?= e(http_build_query(['estado' => $k, 'q' => $q])) ?>"<?= $estado === $k ? ' class="on"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <form class="filters" method="get">
    <input type="hidden" name="estado" value="<?= e($estado) ?>">
    <input class="input" type="search" name="q" value="<?= e($q) ?>" placeholder="Número, cliente o correo">
    <button class="btn">Buscar</button>
  </form>
  <?php if (!$quotes): ?>
    <div class="empty">No hay cotizaciones.</div>
  <?php else: ?>
  <div class="table-wrap"><table class="tbl">
    <thead><tr><th>Número</th><th>Cliente</th><th>Emisión</th><th>Válida hasta</th><th>Estado</th><th class="num">Total</th></tr></thead>
    <tbody>
    <?php foreach ($quotes as $x): ?>
      <tr>
        <td><a class="row-link" href="<?= e(url('admin/cotizacion.php?id=' . $x['id'])) ?>"><?= e($x['number']) ?></a></td>
        <td><a href="<?= e(url('admin/cliente.php?id=' . $x['client_id'])) ?>"><?= e($x['institution']) ?></a></td>
        <td class="nowrap"><?= e(fdate($x['issue_date'])) ?></td>
        <td class="nowrap"><?= e(fdate($x['valid_until'])) ?></td>
        <td><?= badge(quote_status($x)) ?></td>
        <td class="num"><?= e(money($x['total'])) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?= pagination_links($p) ?>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
