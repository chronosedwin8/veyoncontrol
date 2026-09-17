<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$client = require_client();
$cid = (int) $client['id'];

$due = db_one("SELECT COUNT(*) n, COALESCE(SUM(total - amount_paid), 0) amount FROM invoices WHERE client_id = ? AND status IN ('pending','partial')", [$cid]);
$paidYear = (float) db_val("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE client_id = ? AND status = 'approved' AND YEAR(paid_at) = YEAR(CURDATE())", [$cid]);
$licenses = db_all("SELECT * FROM licenses WHERE client_id = ? AND status = 'active' ORDER BY end_date DESC", [$cid]);
$openInvoices = db_all("SELECT * FROM invoices WHERE client_id = ? AND status IN ('pending','partial') ORDER BY due_date LIMIT 5", [$cid]);
$openQuotes = db_all("SELECT * FROM quotes WHERE client_id = ? AND status = 'sent' AND (valid_until IS NULL OR valid_until >= CURDATE()) ORDER BY issue_date DESC LIMIT 5", [$cid]);
$lastPayments = db_all('SELECT p.*, i.number FROM payments p LEFT JOIN invoices i ON i.id = p.invoice_id WHERE p.client_id = ? ORDER BY COALESCE(p.paid_at, p.created_at) DESC LIMIT 5', [$cid]);
$activeCount = count(array_filter($licenses, fn ($l) => $l['end_date'] >= date('Y-m-d')));

$title = 'Resumen';
$area = 'portal';
$nav = 'inicio';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head">
  <div>
    <h1>Hola, <?= e($client['contact_name'] ?: $client['institution']) ?></h1>
    <p><?= e($client['institution']) ?></p>
  </div>
  <div class="actions">
    <a class="btn btn-primary" href="<?= e(url('index.php#planes')) ?>">Comprar o renovar licencia</a>
  </div>
</div>

<div class="grid g-3 mb-3">
  <div class="kpi accent">
    <div class="k-label">Saldo pendiente</div>
    <div class="k-value"><?= e(money($due['amount'])) ?></div>
    <div class="k-sub"><?= (int) $due['n'] ?> factura(s) por pagar</div>
  </div>
  <div class="kpi">
    <div class="k-label">Licencias vigentes</div>
    <div class="k-value"><?= $activeCount ?></div>
    <div class="k-sub"><?= $licenses ? 'Próximo vencimiento: ' . e(fdate(min(array_column(array_filter($licenses, fn ($l) => $l['end_date'] >= date('Y-m-d')) ?: $licenses, 'end_date')))) : 'Aún no tienes licencias' ?></div>
  </div>
  <div class="kpi">
    <div class="k-label">Pagado en <?= date('Y') ?></div>
    <div class="k-value"><?= e(money($paidYear)) ?></div>
    <div class="k-sub">Pagos aprobados</div>
  </div>
</div>

<?php if ($openInvoices): ?>
<div class="card mb-3">
  <div class="card-head"><h2>Facturas por pagar</h2><a class="link small" href="<?= e(url('portal/facturas.php')) ?>">Ver todas</a></div>
  <div class="table-wrap"><table class="tbl">
    <thead><tr><th>Factura</th><th>Vence</th><th>Estado</th><th class="num">Saldo</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($openInvoices as $inv): ?>
      <tr>
        <td><a class="row-link" href="<?= e(url('portal/factura.php?id=' . $inv['id'])) ?>"><?= e($inv['number']) ?></a></td>
        <td><?= e(fdate($inv['due_date'])) ?></td>
        <td><?= badge(invoice_status($inv)) ?></td>
        <td class="num"><?= e(money(invoice_balance($inv))) ?></td>
        <td class="right">
          <form method="post" action="<?= e(url('portal/pagar.php')) ?>" class="inline">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $inv['id'] ?>">
            <button class="btn btn-sm btn-mp" type="submit">Pagar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
<?php endif; ?>

<div class="grid g-2">
  <div class="card">
    <div class="card-head"><h2>Mis licencias</h2></div>
    <?php if (!$licenses): ?>
      <div class="empty">Cuando pagues un plan, tu licencia aparecerá aquí con su vigencia.</div>
    <?php else: foreach ($licenses as $l):
        $start = strtotime($l['start_date']);
        $end = strtotime($l['end_date']);
        $pct = $end > $start ? max(0, min(100, (time() - $start) / ($end - $start) * 100)) : 100;
        $daysLeft = (int) floor(($end - strtotime('today')) / 86400);
      ?>
      <div class="license">
        <div class="ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
        <div class="body">
          <div style="display:flex;justify-content:space-between;gap:.5rem;flex-wrap:wrap">
            <b><?= e($l['description']) ?></b>
            <?php if ($daysLeft < 0): ?><span class="pill pill-danger">Vencida</span>
            <?php elseif ($l['start_date'] > date('Y-m-d')): ?><span class="pill pill-info">Programada</span>
            <?php elseif ($daysLeft <= 30): ?><span class="pill pill-warn">Vence en <?= $daysLeft ?> días</span>
            <?php else: ?><span class="pill pill-ok">Vigente</span><?php endif; ?>
          </div>
          <div class="small muted mb-1"><?= e(fdate($l['start_date'])) ?> → <?= e(fdate($l['end_date'])) ?></div>
          <div class="progress"><span style="width:<?= round($pct) ?>%"></span></div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>

  <div class="stack">
    <?php if ($openQuotes): ?>
    <div class="card">
      <div class="card-head"><h2>Cotizaciones por revisar</h2></div>
      <?php foreach ($openQuotes as $q): ?>
        <div class="list-row">
          <div><div class="t"><?= e($q['number']) ?> · <?= e(money($q['total'])) ?></div><div class="s">Válida hasta <?= e(fdate($q['valid_until'])) ?></div></div>
          <a class="btn btn-sm btn-primary" href="<?= e(url('portal/cotizacion.php?id=' . $q['id'])) ?>">Revisar</a>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-head"><h2>Últimos pagos</h2><a class="link small" href="<?= e(url('portal/pagos.php')) ?>">Historial</a></div>
      <?php if (!$lastPayments): ?>
        <div class="empty">Todavía no hay pagos registrados.</div>
      <?php else: foreach ($lastPayments as $p): ?>
        <div class="list-row">
          <div><div class="t"><?= e(money($p['amount'])) ?></div><div class="s"><?= e(fdate($p['paid_at'] ?: $p['created_at'])) ?> · <?= e(payment_methods()[$p['method']] ?? '') ?><?= $p['number'] ? ' · ' . e($p['number']) : '' ?></div></div>
          <?= badge(payment_status($p['status'])) ?>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
