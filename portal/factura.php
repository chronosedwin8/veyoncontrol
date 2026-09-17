<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$client = require_client();

$doc = db_one("SELECT * FROM invoices WHERE id = ? AND client_id = ? AND status <> 'draft'", [input_int('id'), (int) $client['id']]);
if (!$doc) {
    http_response_code(404);
    flash('error', 'La factura no existe.');
    redirect('portal/facturas.php');
}
$docType = 'invoice';
$items = document_items('invoice', (int) $doc['id']);
$payments = db_all("SELECT * FROM payments WHERE invoice_id = ? AND status NOT IN ('cancelled') ORDER BY COALESCE(paid_at, created_at)", [$doc['id']]);
$hasPending = (bool) array_filter($payments, fn ($p) => in_array($p['status'], ['pending', 'in_process'], true));
$canPay = in_array($doc['status'], ['pending', 'partial'], true) && invoice_balance($doc) > 0;

$title = 'Factura ' . $doc['number'];
$area = 'portal';
$nav = 'facturas';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="doc-toolbar">
  <a class="btn btn-ghost" href="<?= e(url('portal/facturas.php')) ?>">← Facturas</a>
  <div style="display:flex;gap:.5rem;flex-wrap:wrap">
    <button class="btn" type="button" onclick="window.print()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
      Imprimir / PDF
    </button>
    <?php if ($canPay): ?>
      <form method="post" action="<?= e(url('portal/pagar.php')) ?>" class="inline">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $doc['id'] ?>">
        <button class="btn btn-mp" type="submit">Pagar <?= e(money(invoice_balance($doc))) ?> con Mercado Pago</button>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php if ($hasPending): ?>
  <div class="alert alert-warn no-print" style="max-width:880px;margin:0 auto 1rem">Tienes un pago en proceso para esta factura (por ejemplo PSE o efectivo). Se reflejará automáticamente cuando Mercado Pago lo confirme.</div>
<?php endif; ?>
<?php require APP_ROOT . '/app/views/document.php'; ?>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
