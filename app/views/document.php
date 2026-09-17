<?php
/**
 * Factura o cotización imprimible.
 * @var string $docType 'invoice' | 'quote'
 * @var array  $doc
 * @var array  $client
 * @var array  $items
 * @var array  $payments (solo facturas)
 */
$isInvoice = $docType === 'invoice';
$status = $isInvoice ? invoice_status($doc) : quote_status($doc);
$stamp = null;
if ($isInvoice && $doc['status'] === 'paid') {
    $stamp = ['Pagada', 'ok'];
} elseif ($isInvoice && $doc['status'] === 'cancelled') {
    $stamp = ['Anulada', 'danger'];
} elseif (!$isInvoice && in_array($doc['status'], ['rejected', 'expired'], true)) {
    $stamp = [$status[0], 'muted'];
}
$s = settings();
?>
<article class="doc">
  <?php if ($stamp): ?><div class="stamp <?= e($stamp[1]) ?>"><?= e($stamp[0]) ?></div><?php endif; ?>
  <header class="doc-head">
    <div class="co">
      <img src="<?= e(url('assets/img/logo.svg')) ?>" alt="">
      <div>
        <b><?= e($s['company_name'] ?? 'Veyon Control') ?></b>
        <?php if (!empty($s['company_tax_id'])): ?><span>NIT / Tax ID: <?= e($s['company_tax_id']) ?></span><?php endif; ?>
        <?php if (!empty($s['company_address'])): ?><span><?= e($s['company_address']) ?></span><?php endif; ?>
        <?php if (!empty($s['company_city'])): ?><span><?= e($s['company_city']) ?></span><?php endif; ?>
        <?php if (!empty($s['company_email'])): ?><span><?= e($s['company_email']) ?><?= !empty($s['company_phone']) ? ' · ' . e($s['company_phone']) : '' ?></span><?php endif; ?>
      </div>
    </div>
    <div class="meta">
      <div class="type"><?= $isInvoice ? 'Factura' : 'Cotización' ?></div>
      <div class="no"><?= e($doc['number']) ?></div>
      <dl>
        <dt>Fecha</dt><dd><?= e(fdate($doc['issue_date'])) ?></dd>
        <?php if ($isInvoice): ?>
          <dt>Vence</dt><dd><?= e(fdate($doc['due_date'])) ?></dd>
        <?php else: ?>
          <dt>Válida hasta</dt><dd><?= e(fdate($doc['valid_until'])) ?></dd>
        <?php endif; ?>
        <dt>Estado</dt><dd><?= badge($status) ?></dd>
      </dl>
    </div>
  </header>

  <section class="doc-parties">
    <div>
      <h4><?= $isInvoice ? 'Facturado a' : 'Preparada para' ?></h4>
      <p><b><?= e($client['institution']) ?></b><br>
        <?php if ($client['tax_id']): ?>NIT: <?= e($client['tax_id']) ?><br><?php endif; ?>
        <?php if ($client['contact_name']): ?><?= e($client['contact_name']) ?><?= $client['position'] ? ' · ' . e($client['position']) : '' ?><br><?php endif; ?>
        <?= e($client['email']) ?><?= $client['phone'] ? ' · ' . e($client['phone']) : '' ?><br>
        <?php if ($client['address'] || $client['city']): ?><?= e(trim($client['address'] . ', ' . $client['city'], ', ')) ?><?php endif; ?>
      </p>
    </div>
    <div>
      <h4>Condiciones</h4>
      <p>Moneda: pesos colombianos (COP)<br>
        <?php if ($isInvoice): ?>Pago en línea con Mercado Pago o transferencia<?php else: ?>Precios sujetos a la vigencia indicada<?php endif; ?><br>
        Vigencia de licencias: <?= e(setting('license_months', '12')) ?> meses desde la activación
      </p>
    </div>
  </section>

  <table>
    <thead><tr><th>Descripción</th><th class="num">Cant.</th><th class="num">Valor unitario</th><th class="num">Total</th></tr></thead>
    <tbody>
      <?php foreach ($items as $it): ?>
        <tr>
          <td><?= e($it['description']) ?></td>
          <td class="num"><?= e(rtrim(rtrim(number_format((float) $it['quantity'], 2, ',', '.'), '0'), ',')) ?></td>
          <td class="num"><?= e(money($it['unit_price'])) ?></td>
          <td class="num"><?= e(money($it['line_total'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="totals">
    <div class="row"><span>Subtotal</span><b><?= e(money($doc['subtotal'])) ?></b></div>
    <?php if ((float) $doc['discount'] > 0): ?><div class="row"><span>Descuento</span><b>− <?= e(money($doc['discount'])) ?></b></div><?php endif; ?>
    <?php if ((float) $doc['tax_rate'] > 0): ?><div class="row"><span><?= e(setting('tax_label', 'IVA')) ?> (<?= e(num_input($doc['tax_rate'])) ?> %)</span><b><?= e(money($doc['tax_amount'])) ?></b></div><?php endif; ?>
    <div class="row grand"><span>Total</span><span><?= e(money($doc['total'], true)) ?></span></div>
    <?php if ($isInvoice && (float) $doc['amount_paid'] > 0): ?>
      <div class="row"><span>Pagado</span><b><?= e(money($doc['amount_paid'])) ?></b></div>
      <div class="row"><span>Saldo pendiente</span><b><?= e(money(invoice_balance($doc))) ?></b></div>
    <?php endif; ?>
  </div>

  <?php if ($isInvoice && !empty($payments)): ?>
    <h4 class="mt-3 mb-1" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.1em;color:var(--txt-mute)">Pagos registrados</h4>
    <table>
      <thead><tr><th>Fecha</th><th>Medio</th><th>Referencia</th><th>Estado</th><th class="num">Valor</th></tr></thead>
      <tbody>
        <?php foreach ($payments as $p): ?>
          <tr>
            <td><?= e(fdate($p['paid_at'] ?: $p['created_at'], true)) ?></td>
            <td><?= e(payment_methods()[$p['method']] ?? $p['method']) ?></td>
            <td class="mono"><?= e($p['mp_payment_id'] ?: $p['reference']) ?></td>
            <td><?= badge(payment_status($p['status'])) ?></td>
            <td class="num"><?= e(money($p['amount'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <?php if (trim((string) $doc['notes']) !== ''): ?>
    <div class="doc-notes"><b>Observaciones</b><br><?= e($doc['notes']) ?></div>
  <?php endif; ?>

  <footer class="doc-foot"><?= e(setting('invoice_footer')) ?></footer>
</article>
