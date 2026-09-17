<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$client = require_client();

$doc = db_one("SELECT * FROM quotes WHERE id = ? AND client_id = ? AND status <> 'draft'", [input_int('id'), (int) $client['id']]);
if (!$doc) {
    flash('error', 'La cotización no existe.');
    redirect('portal/cotizaciones.php');
}
$expired = $doc['valid_until'] && $doc['valid_until'] < date('Y-m-d');
$actionable = $doc['status'] === 'sent' && !$expired;

if (is_post()) {
    verify_csrf();
    if (!$actionable) {
        flash('warn', 'Esta cotización ya no se puede modificar.');
        redirect('portal/cotizacion.php?id=' . $doc['id']);
    }
    if (input('action') === 'accept') {
        db_update('quotes', ['status' => 'accepted'], 'id = ?', [$doc['id']]);
        log_activity('quote_accepted', 'quote', (int) $doc['id'], $doc['number']);
        $invoiceId = convert_quote_to_invoice((int) $doc['id']);
        flash('ok', 'Aceptaste la cotización. Generamos la factura para que puedas pagarla.');
        redirect('portal/factura.php?id=' . $invoiceId);
    }
    if (input('action') === 'reject') {
        db_update('quotes', ['status' => 'rejected'], 'id = ?', [$doc['id']]);
        log_activity('quote_rejected', 'quote', (int) $doc['id'], $doc['number']);
        flash('ok', 'Registramos que no aceptas esta cotización. Si quieres ajustarla, escríbenos.');
        redirect('portal/cotizacion.php?id=' . $doc['id']);
    }
}

$docType = 'quote';
$items = document_items('quote', (int) $doc['id']);
$payments = [];

$title = 'Cotización ' . $doc['number'];
$area = 'portal';
$nav = 'cotizaciones';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="doc-toolbar">
  <a class="btn btn-ghost" href="<?= e(url('portal/cotizaciones.php')) ?>">← Cotizaciones</a>
  <div style="display:flex;gap:.5rem;flex-wrap:wrap">
    <button class="btn" type="button" onclick="window.print()">Imprimir / PDF</button>
    <?php if ($actionable): ?>
      <form method="post" class="inline" data-confirm="¿Rechazar esta cotización?"><?= csrf_field() ?><input type="hidden" name="action" value="reject"><button class="btn btn-danger">Rechazar</button></form>
      <form method="post" class="inline" data-confirm="Al aceptar se generará la factura por <?= e(money($doc['total'])) ?>. ¿Continuar?"><?= csrf_field() ?><input type="hidden" name="action" value="accept"><button class="btn btn-success">Aceptar y generar factura</button></form>
    <?php elseif ($doc['invoice_id']): ?>
      <a class="btn btn-primary" href="<?= e(url('portal/factura.php?id=' . $doc['invoice_id'])) ?>">Ver factura</a>
    <?php endif; ?>
  </div>
</div>
<?php require APP_ROOT . '/app/views/document.php'; ?>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
