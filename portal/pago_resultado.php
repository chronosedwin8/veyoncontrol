<?php
/**
 * Retorno desde Mercado Pago (back_urls). Consulta el pago directamente a la API
 * para reflejarlo de inmediato, sin depender solo del webhook.
 */
require dirname(__DIR__) . '/app/bootstrap.php';
$client = require_client();

$invoiceId = input_int('inv');
$paymentId = preg_replace('/\D/', '', (string) input('payment_id', (string) input('collection_id')));
$invoice = db_one('SELECT * FROM invoices WHERE id = ? AND client_id = ?', [$invoiceId, (int) $client['id']]);
if (!$invoice) {
    redirect('portal/facturas.php');
}

$payment = null;
$error = null;
if ($paymentId !== '' && mp_configured()) {
    try {
        $payment = mp_get_payment($paymentId);
        if ($payment && ($payment['external_reference'] ?? '') === 'INV-' . $invoiceId) {
            mp_apply_payment($payment);
        } else {
            $payment = null;
        }
    } catch (Throwable $e) {
        error_log('pago_resultado: ' . $e->getMessage());
        $error = 'No pudimos confirmar el pago en este momento; se actualizará automáticamente en unos minutos.';
    }
}
$invoice = db_one('SELECT * FROM invoices WHERE id = ?', [$invoiceId]);
$status = $payment['status'] ?? (string) input('collection_status', (string) input('status'));

$views = [
    'approved'   => ['ok', '¡Pago aprobado!', 'Gracias. Registramos tu pago y la factura quedó actualizada.'],
    'pending'    => ['warn', 'Pago pendiente', 'Tu pago está pendiente de acreditación (PSE, efectivo o revisión). Te lo reflejaremos apenas Mercado Pago lo confirme.'],
    'in_process' => ['warn', 'Pago en revisión', 'Mercado Pago está revisando el pago. Normalmente se resuelve en pocos minutos.'],
    'rejected'   => ['error', 'Pago rechazado', 'El pago no fue aprobado. Puedes intentarlo de nuevo con otro medio de pago.'],
];
$view = $views[$status] ?? (in_array($status, ['null', 'failure', ''], true)
    ? ['info', 'Pago no completado', 'No se completó el pago. Puedes intentarlo de nuevo cuando quieras.']
    : ['info', 'Estado del pago', 'Revisa el estado de tu factura.']);

$title = 'Resultado del pago';
$area = 'portal';
$nav = 'facturas';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="card" style="max-width:640px;margin:2rem auto">
  <div class="card-body center" style="padding:2.4rem 1.6rem">
    <div class="alert alert-<?= e($view[0]) ?>" style="justify-content:center"><b><?= e($view[1]) ?></b></div>
    <p class="mb-2"><?= e($view[2]) ?></p>
    <?php if ($error): ?><p class="small muted mb-2"><?= e($error) ?></p><?php endif; ?>
    <dl class="dl" style="max-width:360px;margin:1.2rem auto;text-align:left">
      <dt>Factura</dt><dd><?= e($invoice['number']) ?></dd>
      <dt>Estado</dt><dd><?= badge(invoice_status($invoice)) ?></dd>
      <dt>Total</dt><dd><?= e(money($invoice['total'])) ?></dd>
      <dt>Saldo</dt><dd><?= e(money(invoice_balance($invoice))) ?></dd>
      <?php if ($paymentId): ?><dt>Operación</dt><dd class="mono"><?= e($paymentId) ?></dd><?php endif; ?>
    </dl>
    <div style="display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap">
      <a class="btn btn-primary" href="<?= e(url('portal/factura.php?id=' . $invoice['id'])) ?>">Ver factura</a>
      <?php if (in_array($invoice['status'], ['pending', 'partial'], true) && $status !== 'pending' && $status !== 'in_process'): ?>
        <form method="post" action="<?= e(url('portal/pagar.php')) ?>" class="inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $invoice['id'] ?>"><button class="btn btn-mp">Intentar de nuevo</button></form>
      <?php endif; ?>
      <a class="btn" href="<?= e(url('portal/index.php')) ?>">Ir al resumen</a>
    </div>
  </div>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
