<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$admin = require_admin();

$kpi = db_one(
    "SELECT
       (SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='approved' AND paid_at >= DATE_FORMAT(CURDATE(),'%Y-%m-01')) AS month_income,
       (SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='approved' AND YEAR(paid_at) = YEAR(CURDATE())) AS year_income,
       (SELECT COALESCE(SUM(total - amount_paid),0) FROM invoices WHERE status IN ('pending','partial')) AS receivable,
       (SELECT COUNT(*) FROM invoices WHERE status IN ('pending','partial') AND due_date < CURDATE()) AS overdue,
       (SELECT COUNT(*) FROM clients WHERE status='active') AS clients,
       (SELECT COUNT(*) FROM licenses WHERE status='active' AND end_date >= CURDATE()) AS licenses,
       (SELECT COUNT(*) FROM licenses WHERE status='active' AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)) AS expiring,
       (SELECT COUNT(*) FROM quotes WHERE status='sent' AND (valid_until IS NULL OR valid_until >= CURDATE())) AS open_quotes"
);

// Ingresos de los últimos 12 meses
$rows = db_all(
    "SELECT DATE_FORMAT(paid_at,'%Y-%m') ym, SUM(amount) total FROM payments
     WHERE status='approved' AND paid_at >= DATE_SUB(DATE_FORMAT(CURDATE(),'%Y-%m-01'), INTERVAL 11 MONTH)
     GROUP BY ym"
);
$byMonth = array_column($rows, 'total', 'ym');
$months = [];
$monthNames = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
for ($i = 11; $i >= 0; $i--) {
    $ts = strtotime(date('Y-m-01') . " -{$i} months");
    $ym = date('Y-m', $ts);
    $months[] = ['label' => $monthNames[(int) date('n', $ts) - 1], 'total' => (float) ($byMonth[$ym] ?? 0)];
}
$maxMonth = max(1, max(array_column($months, 'total')));

$overdue = db_all("SELECT i.*, c.institution FROM invoices i JOIN clients c ON c.id = i.client_id WHERE i.status IN ('pending','partial') ORDER BY i.due_date LIMIT 6");
$recentPayments = db_all("SELECT p.*, c.institution, i.number FROM payments p JOIN clients c ON c.id = p.client_id LEFT JOIN invoices i ON i.id = p.invoice_id ORDER BY p.created_at DESC LIMIT 6");
$expiring = db_all("SELECT l.*, c.institution FROM licenses l JOIN clients c ON c.id = l.client_id WHERE l.status='active' AND l.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 45 DAY) ORDER BY l.end_date LIMIT 6");
$leads = db_all("SELECT * FROM leads WHERE status='new' ORDER BY created_at DESC LIMIT 5");

$title = 'Resumen';
$area = 'admin';
$nav = 'dashboard';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head">
  <div><h1>Resumen</h1><p>Hola, <?= e($admin['name']) ?>. Así va el negocio hoy.</p></div>
  <div class="actions">
    <a class="btn" href="<?= e(url('admin/cliente_form.php')) ?>">Nuevo cliente</a>
    <a class="btn" href="<?= e(url('admin/cotizacion_form.php')) ?>">Nueva cotización</a>
    <a class="btn btn-primary" href="<?= e(url('admin/factura_form.php')) ?>">Nueva factura</a>
  </div>
</div>

<?php if (!mp_configured()): ?>
  <div class="alert alert-warn">Mercado Pago aún no está configurado: los clientes verán las facturas pero no podrán pagarlas en línea. <a class="link" href="<?= e(url('admin/ajustes.php#mercadopago')) ?>">Ver instrucciones</a></div>
<?php endif; ?>

<div class="grid g-4 mb-3">
  <div class="kpi accent"><div class="k-label">Ingresos del mes</div><div class="k-value"><?= e(money($kpi['month_income'])) ?></div><div class="k-sub">Año: <?= e(money($kpi['year_income'])) ?></div></div>
  <div class="kpi"><div class="k-label">Por cobrar</div><div class="k-value"><?= e(money($kpi['receivable'])) ?></div><div class="k-sub"><?= (int) $kpi['overdue'] ?> factura(s) vencida(s)</div></div>
  <div class="kpi"><div class="k-label">Licencias vigentes</div><div class="k-value"><?= (int) $kpi['licenses'] ?></div><div class="k-sub"><?= (int) $kpi['expiring'] ?> vencen en 30 días</div></div>
  <div class="kpi"><div class="k-label">Clientes activos</div><div class="k-value"><?= (int) $kpi['clients'] ?></div><div class="k-sub"><?= (int) $kpi['open_quotes'] ?> cotización(es) abiertas</div></div>
</div>

<div class="grid g-side mb-3">
  <div class="card">
    <div class="card-head"><h2>Ingresos últimos 12 meses</h2><a class="link small" href="<?= e(url('admin/pagos.php')) ?>">Ver pagos</a></div>
    <div class="card-body">
      <div class="bars" role="img" aria-label="Ingresos por mes">
        <?php foreach ($months as $m): ?>
          <div class="bar" title="<?= e($m['label'] . ': ' . money($m['total'])) ?>">
            <b><?= $m['total'] > 0 ? e(number_format($m['total'] / 1000000, 1, ',', '.')) . 'M' : '' ?></b>
            <i style="height:<?= round($m['total'] / $maxMonth * 100) ?>%"></i>
            <small><?= e($m['label']) ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-head"><h2>Solicitudes nuevas</h2><a class="link small" href="<?= e(url('admin/solicitudes.php')) ?>">Ver todas</a></div>
    <?php if (!$leads): ?><div class="empty">Sin solicitudes pendientes.</div><?php endif; ?>
    <?php foreach ($leads as $l): ?>
      <div class="list-row">
        <div><div class="t"><?= e($l['institution']) ?></div><div class="s"><?= e($l['contact_name']) ?> · <?= e($l['equipment']) ?> · <?= e(fdate($l['created_at'])) ?></div></div>
        <a class="btn btn-sm" href="<?= e(url('admin/solicitudes.php?id=' . $l['id'])) ?>">Abrir</a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="grid g-2">
  <div class="card">
    <div class="card-head"><h2>Facturas por cobrar</h2><a class="link small" href="<?= e(url('admin/facturas.php?estado=por_cobrar')) ?>">Ver todas</a></div>
    <?php if (!$overdue): ?><div class="empty">No hay facturas pendientes.</div><?php else: ?>
    <div class="table-wrap"><table class="tbl">
      <thead><tr><th>Factura</th><th>Cliente</th><th>Vence</th><th class="num">Saldo</th></tr></thead>
      <tbody>
      <?php foreach ($overdue as $inv): ?>
        <tr>
          <td><a class="row-link" href="<?= e(url('admin/factura.php?id=' . $inv['id'])) ?>"><?= e($inv['number']) ?></a><br><?= badge(invoice_status($inv)) ?></td>
          <td><?= e($inv['institution']) ?></td>
          <td class="nowrap"><?= e(fdate($inv['due_date'])) ?></td>
          <td class="num"><?= e(money(invoice_balance($inv))) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <?php endif; ?>
  </div>

  <div class="stack">
    <div class="card">
      <div class="card-head"><h2>Últimos pagos</h2></div>
      <?php if (!$recentPayments): ?><div class="empty">Aún no hay pagos.</div><?php endif; ?>
      <?php foreach ($recentPayments as $p): ?>
        <div class="list-row">
          <div><div class="t"><?= e(money($p['amount'])) ?> · <?= e($p['institution']) ?></div><div class="s"><?= e(fdate($p['paid_at'] ?: $p['created_at'], true)) ?> · <?= e(payment_methods()[$p['method']] ?? '') ?><?= $p['number'] ? ' · ' . e($p['number']) : '' ?></div></div>
          <?= badge(payment_status($p['status'])) ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="card">
      <div class="card-head"><h2>Licencias por vencer (45 días)</h2><a class="link small" href="<?= e(url('admin/licencias.php?filtro=por_vencer')) ?>">Ver</a></div>
      <?php if (!$expiring): ?><div class="empty">Ninguna licencia vence pronto.</div><?php endif; ?>
      <?php foreach ($expiring as $l): ?>
        <div class="list-row">
          <div><div class="t"><?= e($l['institution']) ?></div><div class="s"><?= e($l['description']) ?></div></div>
          <span class="pill pill-warn"><?= e(fdate($l['end_date'])) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
