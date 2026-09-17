<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$client = require_client();

$filter = (string) input('estado');
$where = "client_id = ? AND status <> 'draft'";
$params = [(int) $client['id']];
if ($filter === 'por_pagar') {
    $where .= " AND status IN ('pending','partial')";
} elseif ($filter === 'pagadas') {
    $where .= " AND status = 'paid'";
}
$p = paginate((int) db_val("SELECT COUNT(*) FROM invoices WHERE {$where}", $params), 20);
$invoices = db_all("SELECT * FROM invoices WHERE {$where} ORDER BY issue_date DESC, id DESC LIMIT {$p['limit']} OFFSET {$p['offset']}", $params);

$title = 'Facturas';
$area = 'portal';
$nav = 'facturas';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head"><div><h1>Facturas</h1><p>Consulta, descarga y paga tus facturas.</p></div></div>
<div class="card">
  <nav class="tabs">
    <a href="?"<?= $filter === '' ? ' class="on"' : '' ?>>Todas</a>
    <a href="?estado=por_pagar"<?= $filter === 'por_pagar' ? ' class="on"' : '' ?>>Por pagar</a>
    <a href="?estado=pagadas"<?= $filter === 'pagadas' ? ' class="on"' : '' ?>>Pagadas</a>
  </nav>
  <?php if (!$invoices): ?>
    <div class="empty">No hay facturas para mostrar.</div>
  <?php else: ?>
  <div class="table-wrap"><table class="tbl">
    <thead><tr><th>Número</th><th>Fecha</th><th>Vence</th><th>Estado</th><th class="num">Total</th><th class="num">Saldo</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($invoices as $inv): ?>
      <tr>
        <td><a class="row-link" href="<?= e(url('portal/factura.php?id=' . $inv['id'])) ?>"><?= e($inv['number']) ?></a></td>
        <td><?= e(fdate($inv['issue_date'])) ?></td>
        <td><?= e(fdate($inv['due_date'])) ?></td>
        <td><?= badge(invoice_status($inv)) ?></td>
        <td class="num"><?= e(money($inv['total'])) ?></td>
        <td class="num"><?= e(money(invoice_balance($inv))) ?></td>
        <td class="right nowrap">
          <a class="btn btn-sm" href="<?= e(url('portal/factura.php?id=' . $inv['id'])) ?>">Ver</a>
          <?php if (in_array($inv['status'], ['pending', 'partial'], true)): ?>
            <form method="post" action="<?= e(url('portal/pagar.php')) ?>" class="inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $inv['id'] ?>"><button class="btn btn-sm btn-mp">Pagar</button></form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?= pagination_links($p) ?>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
