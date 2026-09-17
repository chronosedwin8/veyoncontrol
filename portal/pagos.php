<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$client = require_client();
$cid = (int) $client['id'];

$p = paginate((int) db_val('SELECT COUNT(*) FROM payments WHERE client_id = ?', [$cid]), 25);
$payments = db_all(
    "SELECT p.*, i.number FROM payments p LEFT JOIN invoices i ON i.id = p.invoice_id
     WHERE p.client_id = ? ORDER BY COALESCE(p.paid_at, p.created_at) DESC LIMIT {$p['limit']} OFFSET {$p['offset']}",
    [$cid]
);
$total = (float) db_val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE client_id = ? AND status = 'approved'", [$cid]);

$title = 'Pagos';
$area = 'portal';
$nav = 'pagos';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head"><div><h1>Historial de pagos</h1><p>Total pagado: <b><?= e(money($total, true)) ?></b></p></div></div>
<div class="card">
  <?php if (!$payments): ?>
    <div class="empty">Aún no hay pagos registrados.</div>
  <?php else: ?>
  <div class="table-wrap"><table class="tbl">
    <thead><tr><th>Fecha</th><th>Factura</th><th>Medio</th><th>Referencia</th><th>Estado</th><th class="num">Valor</th></tr></thead>
    <tbody>
    <?php foreach ($payments as $pay): ?>
      <tr>
        <td class="nowrap"><?= e(fdate($pay['paid_at'] ?: $pay['created_at'], true)) ?></td>
        <td><?php if ($pay['invoice_id']): ?><a class="row-link" href="<?= e(url('portal/factura.php?id=' . $pay['invoice_id'])) ?>"><?= e($pay['number']) ?></a><?php else: ?>—<?php endif; ?></td>
        <td><?= e(payment_methods()[$pay['method']] ?? $pay['method']) ?></td>
        <td class="mono small"><?= e($pay['mp_payment_id'] ?: $pay['reference']) ?></td>
        <td><?= badge(payment_status($pay['status'])) ?></td>
        <td class="num"><?= e(money($pay['amount'])) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?= pagination_links($p) ?>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
