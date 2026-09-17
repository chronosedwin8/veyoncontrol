<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$client = require_client();

$quotes = db_all("SELECT * FROM quotes WHERE client_id = ? AND status <> 'draft' ORDER BY issue_date DESC, id DESC LIMIT 200", [(int) $client['id']]);

$title = 'Cotizaciones';
$area = 'portal';
$nav = 'cotizaciones';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head"><div><h1>Cotizaciones</h1><p>Revisa las propuestas que te enviamos y acéptalas para pagarlas en línea.</p></div>
  <div class="actions"><a class="btn" href="<?= e(url('index.php#contacto')) ?>">Solicitar una cotización</a></div>
</div>
<div class="card">
  <?php if (!$quotes): ?>
    <div class="empty">No tienes cotizaciones todavía.</div>
  <?php else: ?>
  <div class="table-wrap"><table class="tbl">
    <thead><tr><th>Número</th><th>Fecha</th><th>Válida hasta</th><th>Estado</th><th class="num">Total</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($quotes as $q): ?>
      <tr>
        <td><a class="row-link" href="<?= e(url('portal/cotizacion.php?id=' . $q['id'])) ?>"><?= e($q['number']) ?></a></td>
        <td><?= e(fdate($q['issue_date'])) ?></td>
        <td><?= e(fdate($q['valid_until'])) ?></td>
        <td><?= badge(quote_status($q)) ?></td>
        <td class="num"><?= e(money($q['total'])) ?></td>
        <td class="right"><a class="btn btn-sm" href="<?= e(url('portal/cotizacion.php?id=' . $q['id'])) ?>">Ver</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
