<?php
/** Registro / edición de pagos manuales (transferencia, consignación, efectivo…). */
require dirname(__DIR__) . '/app/bootstrap.php';
$admin = require_admin();

$id = input_int('id');
$payment = $id ? db_one('SELECT * FROM payments WHERE id = ?', [$id]) : null;
if ($id && (!$payment || $payment['method'] === 'mercadopago')) {
    flash('error', 'Los pagos de Mercado Pago se actualizan automáticamente y no se editan a mano.');
    redirect('admin/pagos.php');
}

$invoice = null;
if (!$payment && input_int('invoice_id')) {
    $invoice = db_one('SELECT * FROM invoices WHERE id = ?', [input_int('invoice_id')]);
}
$v = $payment ?: [
    'client_id'  => $invoice['client_id'] ?? input_int('client_id'),
    'invoice_id' => $invoice['id'] ?? null,
    'method'     => 'transferencia',
    'amount'     => $invoice ? invoice_balance($invoice) : '',
    'status'     => 'approved',
    'reference'  => '',
    'paid_at'    => date('Y-m-d\TH:i'),
    'notes'      => '',
];
if ($payment) {
    $v['paid_at'] = $payment['paid_at'] ? date('Y-m-d\TH:i', strtotime($payment['paid_at'])) : '';
}
$errors = [];

if (is_post()) {
    verify_csrf();
    if (input('action') === 'delete' && $payment) {
        db_transaction(function () use ($payment) {
            db_exec('DELETE FROM payments WHERE id = ?', [$payment['id']]);
            if ($payment['invoice_id']) {
                recalc_invoice((int) $payment['invoice_id']);
            }
        });
        log_activity('payment_deleted', 'payment', (int) $payment['id'], money($payment['amount']));
        flash('ok', 'Pago eliminado.');
        redirect('admin/cliente.php?id=' . $payment['client_id'] . '&tab=pagos');
    }

    $methods = payment_methods();
    unset($methods['mercadopago']);
    $v = [
        'client_id'  => input_int('client_id'),
        'invoice_id' => input_int('invoice_id') ?: null,
        'method'     => isset($methods[input('method')]) ? (string) input('method') : 'otro',
        'amount'     => parse_money(input('amount')),
        'status'     => in_array(input('status'), ['approved', 'pending', 'rejected', 'refunded', 'cancelled'], true) ? (string) input('status') : 'approved',
        'reference'  => mb_substr((string) input('reference'), 0, 120),
        'paid_at'    => (string) input('paid_at'),
        'notes'      => mb_substr((string) input('notes'), 0, 2000),
    ];

    if (!db_val('SELECT id FROM clients WHERE id = ?', [$v['client_id']])) {
        $errors[] = 'Selecciona un cliente.';
    }
    if ($v['invoice_id'] && !db_val('SELECT id FROM invoices WHERE id = ? AND client_id = ?', [$v['invoice_id'], $v['client_id']])) {
        $errors[] = 'La factura no pertenece al cliente seleccionado.';
    }
    if ($v['amount'] <= 0) {
        $errors[] = 'El valor debe ser mayor que cero.';
    }
    $ts = strtotime($v['paid_at']);
    if (!$ts) {
        $errors[] = 'La fecha del pago no es válida.';
    }

    if (!$errors) {
        $data = $v;
        $data['paid_at'] = date('Y-m-d H:i:s', $ts);
        $data['currency'] = 'COP';
        if ($payment) {
            db_transaction(function () use ($data, $payment) {
                db_update('payments', $data, 'id = ?', [$payment['id']]);
                foreach (array_unique(array_filter([(int) $payment['invoice_id'], (int) $data['invoice_id']])) as $invId) {
                    recalc_invoice($invId);
                }
            });
            log_activity('payment_updated', 'payment', (int) $payment['id'], money($data['amount']));
            flash('ok', 'Pago actualizado.');
        } else {
            $data['created_by'] = $admin['id'];
            $newId = record_manual_payment($data);
            log_activity('payment_recorded', 'payment', $newId, money($data['amount']) . ' ' . $data['method']);
            flash('ok', 'Pago registrado.');
        }
        redirect($data['invoice_id'] ? 'admin/factura.php?id=' . $data['invoice_id'] : 'admin/cliente.php?id=' . $data['client_id'] . '&tab=pagos');
    }
}

$clients = db_all("SELECT id, institution FROM clients WHERE status = 'active' OR id = ? ORDER BY institution", [(int) $v['client_id']]);
$openInvoices = db_all(
    "SELECT i.id, i.client_id, i.number, i.total, i.amount_paid, i.status FROM invoices i
     WHERE i.status IN ('pending','partial') OR i.id = ? ORDER BY i.number DESC",
    [(int) $v['invoice_id']]
);

$title = $payment ? 'Editar pago' : 'Registrar pago';
$area = 'admin';
$nav = 'pagos';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head"><div><div class="crumbs"><a href="<?= e(url('admin/pagos.php')) ?>">Pagos</a></div><h1><?= e($title) ?></h1><p>Para pagos recibidos fuera de Mercado Pago: transferencias, consignaciones o efectivo.</p></div></div>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
<form class="card" method="post" style="max-width:820px">
  <div class="card-body">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field">
        <label for="client_id">Cliente <span class="req">*</span></label>
        <select id="client_id" name="client_id" required>
          <option value="">Selecciona…</option>
          <?php foreach ($clients as $c): ?><option value="<?= (int) $c['id'] ?>"<?= (int) $v['client_id'] === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['institution']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="invoice_id">Factura (opcional)</label>
        <select id="invoice_id" name="invoice_id">
          <option value="">Sin factura (abono a cuenta)</option>
          <?php foreach ($openInvoices as $inv): ?>
            <option value="<?= (int) $inv['id'] ?>" data-client="<?= (int) $inv['client_id'] ?>" data-balance="<?= e(num_input(invoice_balance($inv))) ?>"<?= (int) $v['invoice_id'] === (int) $inv['id'] ? ' selected' : '' ?>><?= e($inv['number']) ?> — saldo <?= e(money(invoice_balance($inv))) ?></option>
          <?php endforeach; ?>
        </select>
        <div class="hint">Al asociarlo, la factura se marca como pagada o parcial automáticamente.</div>
      </div>
      <div class="field"><label for="amount">Valor (COP) <span class="req">*</span></label><input id="amount" name="amount" required inputmode="decimal" value="<?= e(is_numeric($v['amount']) ? num_input($v['amount']) : $v['amount']) ?>"></div>
      <div class="field"><label for="paid_at">Fecha del pago <span class="req">*</span></label><input id="paid_at" name="paid_at" type="datetime-local" required value="<?= e($v['paid_at']) ?>"></div>
      <div class="field">
        <label for="method">Medio</label>
        <select id="method" name="method"><?php foreach (payment_methods() as $k => $label): if ($k === 'mercadopago') { continue; } ?><option value="<?= e($k) ?>"<?= $v['method'] === $k ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select>
      </div>
      <div class="field">
        <label for="status">Estado</label>
        <select id="status" name="status"><?php foreach (['approved', 'pending', 'rejected', 'refunded', 'cancelled'] as $s): ?><option value="<?= $s ?>"<?= $v['status'] === $s ? ' selected' : '' ?>><?= e(payment_status($s)[0]) ?></option><?php endforeach; ?></select>
      </div>
      <div class="field full"><label for="reference">Referencia / comprobante</label><input id="reference" name="reference" value="<?= e($v['reference']) ?>" placeholder="Número de transacción, banco…"></div>
      <div class="field full"><label for="notes">Notas</label><textarea id="notes" name="notes" rows="3"><?= e($v['notes']) ?></textarea></div>
    </div>
    <div style="display:flex;gap:.5rem;justify-content:space-between;flex-wrap:wrap">
      <button class="btn btn-primary" type="submit"><?= $payment ? 'Guardar cambios' : 'Registrar pago' ?></button>
    </div>
  </div>
</form>
<?php if ($payment): ?>
  <form method="post" class="mt-2" data-confirm="¿Eliminar este pago? La factura asociada se recalculará."><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn btn-danger btn-sm">Eliminar pago</button></form>
<?php endif; ?>
<script>
  // Filtra las facturas por cliente y sugiere el saldo
  (function () {
    var client = document.getElementById('client_id'), inv = document.getElementById('invoice_id'), amount = document.getElementById('amount');
    function filter() {
      Array.prototype.forEach.call(inv.options, function (o) {
        if (!o.value) return;
        var show = !client.value || o.getAttribute('data-client') === client.value;
        o.hidden = !show;
        if (!show && o.selected) inv.value = '';
      });
    }
    client.addEventListener('change', filter);
    inv.addEventListener('change', function () {
      var o = inv.options[inv.selectedIndex];
      if (o && o.value) {
        amount.value = o.getAttribute('data-balance');
        client.value = o.getAttribute('data-client');
      }
    });
    filter();
  })();
</script>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
