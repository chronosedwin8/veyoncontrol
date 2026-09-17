<?php
/** Compra en línea de un plan: resumen -> factura -> Mercado Pago. */
require __DIR__ . '/app/bootstrap.php';

$plan = db_one('SELECT * FROM plans WHERE slug = ? AND active = 1 AND online_purchase = 1 AND price_cop > 0', [(string) input('plan')]);
if (!$plan) {
    redirect('index.php#planes');
}

$client = current_client();
$taxRate = (float) setting('tax_rate', '0');
$totals = compute_totals([['line_total' => (float) $plan['price_cop']]], 0, $taxRate);

if (is_post()) {
    verify_csrf();
    if (!$client) {
        redirect('portal/login.php?next=' . rawurlencode(url('checkout.php?plan=' . $plan['slug'])));
    }
    $invoiceId = create_plan_invoice((int) $client['id'], $plan);
    $invoice = db_one('SELECT * FROM invoices WHERE id = ?', [$invoiceId]);

    if (!mp_configured()) {
        flash('warn', 'Generamos la factura ' . $invoice['number'] . '. El pago en línea estará disponible muy pronto; también puedes coordinarlo con nosotros.');
        redirect('portal/factura.php?id=' . $invoiceId);
    }
    try {
        $checkoutUrl = mp_create_preference($invoice, $client);
        header('Location: ' . $checkoutUrl, true, 303);
        exit;
    } catch (Throwable $e) {
        error_log('checkout: ' . $e->getMessage());
        flash('error', 'Generamos la factura ' . $invoice['number'] . ', pero no pudimos conectar con Mercado Pago. Inténtalo de nuevo desde aquí.');
        redirect('portal/factura.php?id=' . $invoiceId);
    }
}

$page = [
    'title'       => 'Comprar ' . $plan['name'] . ' — Veyon Control',
    'description' => 'Compra en línea de la ' . $plan['name'] . ' con pago seguro por Mercado Pago.',
    'active'      => '',
];
require APP_ROOT . '/app/views/public_header.php';
$next = url('checkout.php?plan=' . $plan['slug']);
?>
<section class="auth-wrap">
  <div class="container">
    <div class="section-head left" style="margin-bottom:2rem">
      <span class="eyebrow">Compra en línea</span>
      <h1 style="font-size:clamp(2rem,4vw,2.8rem)">Confirma tu <span class="grad-text">licencia</span></h1>
    </div>

    <div class="auth-grid">
      <div class="panel">
        <span class="badge cyan"><?= e($plan['capacity_label']) ?></span>
        <h2 class="mt-2" style="font-size:1.6rem"><?= e($plan['name']) ?></h2>
        <p class="mt-1"><?= e($plan['subtitle']) ?></p>
        <ul class="fr-list mt-3">
          <?php foreach (lines($plan['features']) as $f): ?><li><span class="chk">✓</span><span><?= e($f) ?></span></li><?php endforeach; ?>
        </ul>
        <a class="link small" href="<?= e(url('index.php#planes')) ?>">← Elegir otro plan</a>
      </div>

      <div class="panel summary-box">
        <h3 class="mb-2">Resumen</h3>
        <div class="summary-line"><span><?= e($plan['name']) ?> · <?= e($plan['period_label']) ?></span><b><?= e(money($plan['price_cop'])) ?></b></div>
        <?php if ($taxRate > 0): ?><div class="summary-line"><span><?= e(setting('tax_label', 'IVA')) ?> (<?= e(num_input($taxRate)) ?> %)</span><b><?= e(money($totals['tax_amount'])) ?></b></div><?php endif; ?>
        <div class="summary-line"><span>Total a pagar</span><span class="summary-total"><?= e(money($totals['total'])) ?> <small class="muted" style="font-size:.8rem">COP</small></span></div>

        <?php if ($client): ?>
          <div class="alert alert-info mt-2">Comprando como <b><?= e($client['institution']) ?></b> (<?= e($client['email']) ?>). La factura quedará en tu portal.</div>
          <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="plan" value="<?= e($plan['slug']) ?>">
            <button class="btn btn-primary btn-lg" style="width:100%" type="submit">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Pagar con Mercado Pago
            </button>
          </form>
        <?php else: ?>
          <p class="mt-2 mb-2 small muted">Para emitir la factura a nombre de tu institución necesitas una cuenta. Solo toma un minuto.</p>
          <div style="display:grid;gap:.6rem">
            <a class="btn btn-primary btn-lg" href="<?= e(url('portal/registro.php?next=' . rawurlencode($next))) ?>">Crear cuenta y continuar</a>
            <a class="btn btn-ghost" href="<?= e(url('portal/login.php?next=' . rawurlencode($next))) ?>">Ya tengo cuenta</a>
          </div>
        <?php endif; ?>
        <div class="pay-methods"><span>Tarjeta de crédito</span><span>Tarjeta débito</span><span>PSE</span><span>Efecty</span></div>
        <p class="small muted mt-2">El pago se procesa en el sitio seguro de Mercado Pago. No almacenamos datos de tarjetas.</p>
      </div>
    </div>
  </div>
</section>
<?php require APP_ROOT . '/app/views/public_footer.php'; ?>
