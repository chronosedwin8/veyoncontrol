<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$id = input_int('id');
$doc = db_one('SELECT * FROM invoices WHERE id = ?', [$id]);
if (!$doc) {
    flash('error', 'Factura no encontrada.');
    redirect('admin/facturas.php');
}

if (is_post()) {
    verify_csrf();
    $action = (string) input('action');
    try {
        switch ($action) {
            case 'issue':
                if ($doc['status'] === 'draft') {
                    db_update('invoices', ['status' => 'pending'], 'id = ?', [$id]);
                    recalc_invoice($id);
                    log_activity('invoice_issued', 'invoice', $id, $doc['number']);
                    flash('ok', 'Factura emitida. Ya es visible en el portal del cliente.');
                }
                break;
            case 'cancel':
                $approved = (int) db_val("SELECT COUNT(*) FROM payments WHERE invoice_id = ? AND status = 'approved'", [$id]);
                if ($approved) {
                    flash('error', 'No se puede anular una factura con pagos aprobados. Anula o reembolsa primero los pagos.');
                } else {
                    db_update('invoices', ['status' => 'cancelled'], 'id = ?', [$id]);
                    log_activity('invoice_cancelled', 'invoice', $id, $doc['number']);
                    flash('ok', 'Factura anulada.');
                }
                break;
            case 'reopen':
                if ($doc['status'] === 'cancelled') {
                    db_update('invoices', ['status' => 'pending'], 'id = ?', [$id]);
                    recalc_invoice($id);
                    log_activity('invoice_reopened', 'invoice', $id, $doc['number']);
                    flash('ok', 'Factura reactivada.');
                }
                break;
            case 'delete':
                if ($doc['status'] === 'draft' && !db_val('SELECT COUNT(*) FROM payments WHERE invoice_id = ?', [$id])) {
                    db_exec('DELETE FROM invoices WHERE id = ?', [$id]);
                    db_exec('UPDATE quotes SET invoice_id = NULL, status = ? WHERE invoice_id = ?', ['accepted', $id]);
                    log_activity('invoice_deleted', 'invoice', $id, $doc['number']);
                    flash('ok', 'Borrador eliminado.');
                    redirect('admin/facturas.php');
                }
                flash('error', 'Solo se pueden eliminar borradores sin pagos.');
                break;
            case 'sync':
                $n = mp_sync_invoice($id);
                flash('ok', $n ? "Se sincronizaron {$n} pago(s) desde Mercado Pago." : 'Mercado Pago no reporta pagos para esta factura.');
                break;
            case 'paylink':
                if (!in_array($doc['status'], ['pending', 'partial'], true)) {
                    throw new RuntimeException('La factura no está pendiente de pago.');
                }
                $client = db_one('SELECT * FROM clients WHERE id = ?', [$doc['client_id']]);
                $_SESSION['last_paylink'][$id] = mp_create_preference($doc, $client);
                flash('ok', 'Link de pago de Mercado Pago generado (válido 3 días).');
                break;
        }
    } catch (Throwable $e) {
        error_log('admin factura: ' . $e->getMessage());
        flash('error', $e->getMessage());
    }
    redirect('admin/factura.php?id=' . $id);
}

$client = db_one('SELECT * FROM clients WHERE id = ?', [$doc['client_id']]);
$items = document_items('invoice', $id);
$payments = db_all('SELECT * FROM payments WHERE invoice_id = ? ORDER BY COALESCE(paid_at, created_at)', [$id]);
$licenses = db_all('SELECT * FROM licenses WHERE invoice_id = ?', [$id]);
$quote = $doc['quote_id'] ? db_one('SELECT id, number FROM quotes WHERE id = ?', [$doc['quote_id']]) : null;
$paylink = $_SESSION['last_paylink'][$id] ?? null;
$docType = 'invoice';
$editable = (float) $doc['amount_paid'] == 0 && !in_array($doc['status'], ['paid', 'cancelled'], true);
$open = in_array($doc['status'], ['pending', 'partial'], true);

$title = 'Factura ' . $doc['number'];
$area = 'admin';
$nav = 'facturas';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head no-print">
  <div>
    <div class="crumbs"><a href="<?= e(url('admin/facturas.php')) ?>">Facturas</a> / <a href="<?= e(url('admin/cliente.php?id=' . $client['id'])) ?>"><?= e($client['institution']) ?></a></div>
    <h1>Factura <?= e($doc['number']) ?> <?= badge(invoice_status($doc)) ?></h1>
    <?php if ($quote): ?><p>Generada desde la cotización <a class="link" href="<?= e(url('admin/cotizacion.php?id=' . $quote['id'])) ?>"><?= e($quote['number']) ?></a></p><?php endif; ?>
  </div>
  <div class="actions">
    <button class="btn" type="button" onclick="window.print()">Imprimir / PDF</button>
    <?php if ($editable): ?><a class="btn" href="<?= e(url('admin/factura_form.php?id=' . $id)) ?>">Editar</a><?php endif; ?>
    <?php if ($doc['status'] === 'draft'): ?>
      <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="issue"><button class="btn btn-primary">Emitir factura</button></form>
    <?php endif; ?>
    <?php if ($open): ?>
      <a class="btn btn-success" href="<?= e(url('admin/pago_form.php?invoice_id=' . $id)) ?>">Registrar pago</a>
    <?php endif; ?>
  </div>
</div>

<div class="grid g-side">
  <div><?php require APP_ROOT . '/app/views/document.php'; ?></div>

  <div class="stack no-print">
    <div class="card">
      <div class="card-head"><h3>Cobro</h3></div>
      <div class="card-body">
        <dl class="dl mb-2">
          <dt>Total</dt><dd><?= e(money($doc['total'])) ?></dd>
          <dt>Pagado</dt><dd><?= e(money($doc['amount_paid'])) ?></dd>
          <dt>Saldo</dt><dd><b><?= e(money(invoice_balance($doc))) ?></b></dd>
          <?php if ($doc['paid_at']): ?><dt>Pagada el</dt><dd><?= e(fdate($doc['paid_at'], true)) ?></dd><?php endif; ?>
        </dl>
        <?php if ($open): ?>
          <?php if (mp_configured()): ?>
            <form method="post" class="mb-1"><?= csrf_field() ?><input type="hidden" name="action" value="paylink"><button class="btn btn-mp btn-block">Generar link de pago Mercado Pago</button></form>
            <?php if ($paylink): ?>
              <div class="copy-field mb-1"><input class="input" id="paylink" value="<?= e($paylink) ?>" readonly><button class="btn" type="button" data-copy-target="paylink">Copiar</button></div>
            <?php endif; ?>
          <?php else: ?>
            <p class="small muted mb-1">Configura Mercado Pago para generar links de pago.</p>
          <?php endif; ?>
          <p class="small muted">El cliente también puede pagar desde su portal.</p>
        <?php endif; ?>
        <?php if (mp_configured() && $doc['status'] !== 'draft'): ?>
          <form method="post" class="mt-1"><?= csrf_field() ?><input type="hidden" name="action" value="sync"><button class="btn btn-sm btn-block">Sincronizar pagos con Mercado Pago</button></form>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($licenses): ?>
    <div class="card">
      <div class="card-head"><h3>Licencias activadas</h3></div>
      <?php foreach ($licenses as $l): ?>
        <div class="list-row"><div><div class="t"><?= e($l['description']) ?></div><div class="s"><?= e(fdate($l['start_date'])) ?> → <?= e(fdate($l['end_date'])) ?></div></div></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($payments): ?>
    <div class="card">
      <div class="card-head"><h3>Movimientos</h3></div>
      <?php foreach ($payments as $pay): ?>
        <div class="list-row">
          <div><div class="t"><?= e(money($pay['amount'])) ?> · <?= e(payment_methods()[$pay['method']] ?? '') ?></div><div class="s"><?= e(fdate($pay['paid_at'] ?: $pay['created_at'], true)) ?><?= $pay['mp_status_detail'] ? ' · ' . e($pay['mp_status_detail']) : '' ?></div></div>
          <div style="text-align:right"><?= badge(payment_status($pay['status'])) ?><?php if ($pay['method'] !== 'mercadopago'): ?><br><a class="link small" href="<?= e(url('admin/pago_form.php?id=' . $pay['id'])) ?>">Editar</a><?php endif; ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-head"><h3>Más acciones</h3></div>
      <div class="card-body stack">
        <?php if ($open || $doc['status'] === 'draft'): ?>
          <form method="post" data-confirm="¿Anular la factura <?= e($doc['number']) ?>?"><?= csrf_field() ?><input type="hidden" name="action" value="cancel"><button class="btn btn-danger btn-block">Anular factura</button></form>
        <?php endif; ?>
        <?php if ($doc['status'] === 'cancelled'): ?>
          <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="reopen"><button class="btn btn-block">Reactivar factura</button></form>
        <?php endif; ?>
        <?php if ($doc['status'] === 'draft' && !$payments): ?>
          <form method="post" data-confirm="¿Eliminar este borrador?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn btn-danger btn-block">Eliminar borrador</button></form>
        <?php endif; ?>
        <p class="small muted">Creada el <?= e(fdate($doc['created_at'], true)) ?><?= $doc['mp_preference_id'] ? '<br>Preferencia MP: <span class="mono">' . e($doc['mp_preference_id']) . '</span>' : '' ?></p>
      </div>
    </div>
  </div>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
