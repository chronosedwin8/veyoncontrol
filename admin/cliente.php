<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$id = input_int('id');
$client = db_one('SELECT * FROM clients WHERE id = ?', [$id]);
if (!$client) {
    flash('error', 'Cliente no encontrado.');
    redirect('admin/clientes.php');
}

$inviteLink = null;
if (is_post()) {
    verify_csrf();
    $action = (string) input('action');
    if ($action === 'invite') {
        $token = create_password_reset('client', $id, 72);
        $inviteLink = abs_url('portal/restablecer.php?token=' . $token);
        send_mail($client['email'], 'Acceso al portal de clientes - ' . setting('company_name'), "Hola,\n\nTe damos acceso al portal de clientes de " . setting('company_name') . " para {$client['institution']}.\nDefine tu contraseña aquí (enlace válido 72 horas):\n\n{$inviteLink}\n\nDesde el portal podrás ver facturas, cotizaciones, pagos y licencias, y pagar en línea.");
        log_activity('client_invite_link', 'client', $id);
        flash('ok', 'Enlace de acceso generado (válido 72 horas). Se intentó enviar por correo; también puedes copiarlo y compartirlo.');
    } elseif ($action === 'delete') {
        $hasDocs = db_val('SELECT (SELECT COUNT(*) FROM invoices WHERE client_id = ?) + (SELECT COUNT(*) FROM quotes WHERE client_id = ?) + (SELECT COUNT(*) FROM payments WHERE client_id = ?)', [$id, $id, $id]);
        if ($hasDocs) {
            flash('error', 'No se puede eliminar un cliente con documentos o pagos. Márcalo como inactivo.');
            redirect('admin/cliente.php?id=' . $id);
        }
        db_exec('DELETE FROM clients WHERE id = ?', [$id]);
        log_activity('client_deleted', 'client', $id, $client['institution']);
        flash('ok', 'Cliente eliminado.');
        redirect('admin/clientes.php');
    }
}

$tab = (string) input('tab', 'resumen');
$stats = db_one(
    "SELECT
      (SELECT COALESCE(SUM(amount),0) FROM payments WHERE client_id = ? AND status='approved') paid,
      (SELECT COALESCE(SUM(total - amount_paid),0) FROM invoices WHERE client_id = ? AND status IN ('pending','partial')) balance,
      (SELECT COUNT(*) FROM invoices WHERE client_id = ?) invoices,
      (SELECT COUNT(*) FROM quotes WHERE client_id = ?) quotes",
    [$id, $id, $id, $id]
);
$invoices = db_all('SELECT * FROM invoices WHERE client_id = ? ORDER BY issue_date DESC, id DESC', [$id]);
$quotes = db_all('SELECT * FROM quotes WHERE client_id = ? ORDER BY issue_date DESC, id DESC', [$id]);
$payments = db_all('SELECT p.*, i.number FROM payments p LEFT JOIN invoices i ON i.id = p.invoice_id WHERE p.client_id = ? ORDER BY COALESCE(p.paid_at, p.created_at) DESC', [$id]);
$licenses = db_all('SELECT * FROM licenses WHERE client_id = ? ORDER BY end_date DESC', [$id]);

$title = $client['institution'];
$area = 'admin';
$nav = 'clientes';
require APP_ROOT . '/app/views/panel_header.php';
$tabUrl = fn ($t) => url('admin/cliente.php?id=' . $id . '&tab=' . $t);
?>
<div class="page-head">
  <div>
    <div class="crumbs"><a href="<?= e(url('admin/clientes.php')) ?>">Clientes</a></div>
    <h1><?= e($client['institution']) ?> <?php if ($client['status'] === 'inactive'): ?><span class="pill pill-muted">Inactivo</span><?php endif; ?></h1>
    <p><?= e($client['contact_name']) ?> · <?= e($client['email']) ?></p>
  </div>
  <div class="actions">
    <a class="btn" href="<?= e(url('admin/cliente_form.php?id=' . $id)) ?>">Editar</a>
    <a class="btn" href="<?= e(url('admin/pago_form.php?client_id=' . $id)) ?>">Registrar pago</a>
    <a class="btn" href="<?= e(url('admin/cotizacion_form.php?client_id=' . $id)) ?>">Nueva cotización</a>
    <a class="btn btn-primary" href="<?= e(url('admin/factura_form.php?client_id=' . $id)) ?>">Nueva factura</a>
  </div>
</div>

<?php if ($inviteLink): ?>
  <div class="alert alert-info" style="display:block">
    <b>Enlace de acceso al portal</b>
    <div class="copy-field mt-1"><input class="input" id="invite-link" value="<?= e($inviteLink) ?>" readonly><button type="button" class="btn" data-copy-target="invite-link">Copiar</button></div>
  </div>
<?php endif; ?>

<div class="grid g-4 mb-3">
  <div class="kpi"><div class="k-label">Total pagado</div><div class="k-value"><?= e(money($stats['paid'])) ?></div></div>
  <div class="kpi"><div class="k-label">Saldo pendiente</div><div class="k-value"><?= e(money($stats['balance'])) ?></div></div>
  <div class="kpi"><div class="k-label">Facturas</div><div class="k-value"><?= (int) $stats['invoices'] ?></div></div>
  <div class="kpi"><div class="k-label">Cotizaciones</div><div class="k-value"><?= (int) $stats['quotes'] ?></div></div>
</div>

<div class="card">
  <nav class="tabs">
    <?php foreach (['resumen' => 'Datos', 'facturas' => 'Facturas', 'cotizaciones' => 'Cotizaciones', 'pagos' => 'Pagos', 'licencias' => 'Licencias'] as $k => $label): ?>
      <a href="<?= e($tabUrl($k)) ?>"<?= $tab === $k ? ' class="on"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>

  <?php if ($tab === 'facturas'): ?>
    <?php if (!$invoices): ?><div class="empty">Sin facturas.</div><?php else: ?>
    <div class="table-wrap"><table class="tbl">
      <thead><tr><th>Número</th><th>Fecha</th><th>Vence</th><th>Estado</th><th class="num">Total</th><th class="num">Saldo</th></tr></thead>
      <tbody><?php foreach ($invoices as $inv): ?>
        <tr><td><a class="row-link" href="<?= e(url('admin/factura.php?id=' . $inv['id'])) ?>"><?= e($inv['number']) ?></a></td><td><?= e(fdate($inv['issue_date'])) ?></td><td><?= e(fdate($inv['due_date'])) ?></td><td><?= badge(invoice_status($inv)) ?></td><td class="num"><?= e(money($inv['total'])) ?></td><td class="num"><?= e(money(invoice_balance($inv))) ?></td></tr>
      <?php endforeach; ?></tbody>
    </table></div>
    <?php endif; ?>

  <?php elseif ($tab === 'cotizaciones'): ?>
    <?php if (!$quotes): ?><div class="empty">Sin cotizaciones.</div><?php else: ?>
    <div class="table-wrap"><table class="tbl">
      <thead><tr><th>Número</th><th>Fecha</th><th>Válida hasta</th><th>Estado</th><th class="num">Total</th></tr></thead>
      <tbody><?php foreach ($quotes as $q): ?>
        <tr><td><a class="row-link" href="<?= e(url('admin/cotizacion.php?id=' . $q['id'])) ?>"><?= e($q['number']) ?></a></td><td><?= e(fdate($q['issue_date'])) ?></td><td><?= e(fdate($q['valid_until'])) ?></td><td><?= badge(quote_status($q)) ?></td><td class="num"><?= e(money($q['total'])) ?></td></tr>
      <?php endforeach; ?></tbody>
    </table></div>
    <?php endif; ?>

  <?php elseif ($tab === 'pagos'): ?>
    <?php if (!$payments): ?><div class="empty">Sin pagos.</div><?php else: ?>
    <div class="table-wrap"><table class="tbl">
      <thead><tr><th>Fecha</th><th>Factura</th><th>Medio</th><th>Referencia</th><th>Estado</th><th class="num">Valor</th><th></th></tr></thead>
      <tbody><?php foreach ($payments as $pay): ?>
        <tr>
          <td class="nowrap"><?= e(fdate($pay['paid_at'] ?: $pay['created_at'], true)) ?></td>
          <td><?= $pay['invoice_id'] ? '<a class="row-link" href="' . e(url('admin/factura.php?id=' . $pay['invoice_id'])) . '">' . e($pay['number']) . '</a>' : '—' ?></td>
          <td><?= e(payment_methods()[$pay['method']] ?? $pay['method']) ?></td>
          <td class="mono small"><?= e($pay['mp_payment_id'] ?: $pay['reference']) ?></td>
          <td><?= badge(payment_status($pay['status'])) ?></td>
          <td class="num"><?= e(money($pay['amount'])) ?></td>
          <td class="right"><?php if ($pay['method'] !== 'mercadopago'): ?><a class="btn btn-sm" href="<?= e(url('admin/pago_form.php?id=' . $pay['id'])) ?>">Editar</a><?php endif; ?></td>
        </tr>
      <?php endforeach; ?></tbody>
      <tfoot><tr><td colspan="5">Total aprobado</td><td class="num"><?= e(money($stats['paid'])) ?></td><td></td></tr></tfoot>
    </table></div>
    <?php endif; ?>

  <?php elseif ($tab === 'licencias'): ?>
    <?php if (!$licenses): ?><div class="empty">Sin licencias. Se crean automáticamente al pagar una factura con planes.</div><?php else: ?>
    <div class="table-wrap"><table class="tbl">
      <thead><tr><th>Licencia</th><th>Inicio</th><th>Fin</th><th>Estado</th><th></th></tr></thead>
      <tbody><?php foreach ($licenses as $l): ?>
        <tr>
          <td><?= e($l['description']) ?></td><td><?= e(fdate($l['start_date'])) ?></td><td><?= e(fdate($l['end_date'])) ?></td>
          <td><?= $l['status'] === 'cancelled' ? badge(['Cancelada', 'muted']) : ($l['end_date'] < date('Y-m-d') ? badge(['Vencida', 'danger']) : badge(['Vigente', 'ok'])) ?></td>
          <td class="right"><a class="btn btn-sm" href="<?= e(url('admin/licencias.php?edit=' . $l['id'])) ?>">Editar</a></td>
        </tr>
      <?php endforeach; ?></tbody>
    </table></div>
    <?php endif; ?>

  <?php else: ?>
    <div class="card-body grid g-2">
      <dl class="dl">
        <dt>Institución</dt><dd><?= e($client['institution']) ?></dd>
        <dt>NIT</dt><dd><?= e($client['tax_id'] ?: '—') ?></dd>
        <dt>Contacto</dt><dd><?= e($client['contact_name'] ?: '—') ?><?= $client['position'] ? ' · ' . e($client['position']) : '' ?></dd>
        <dt>Correo</dt><dd><a class="link" href="mailto:<?= e($client['email']) ?>"><?= e($client['email']) ?></a></dd>
        <dt>Teléfono</dt><dd><?= e($client['phone'] ?: '—') ?></dd>
        <dt>Dirección</dt><dd><?= e(trim($client['address'] . ', ' . $client['city'] . ', ' . $client['country'], ', ') ?: '—') ?></dd>
        <dt>Cliente desde</dt><dd><?= e(fdate($client['created_at'])) ?></dd>
        <dt>Último ingreso</dt><dd><?= e(fdate($client['last_login_at'], true)) ?></dd>
      </dl>
      <div class="stack">
        <?php if (trim((string) $client['notes']) !== ''): ?>
          <div><div class="label">Notas internas</div><p style="white-space:pre-line" class="small"><?= e($client['notes']) ?></p></div>
        <?php endif; ?>
        <div>
          <div class="label">Acceso al portal</div>
          <p class="small muted mb-1"><?= $client['password_hash'] ? 'El cliente ya tiene contraseña. Puedes generar un enlace para que la restablezca.' : 'El cliente aún no tiene contraseña. Genera un enlace de invitación.' ?></p>
          <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="invite"><button class="btn btn-sm"><?= $client['password_hash'] ? 'Enlace para restablecer contraseña' : 'Generar invitación al portal' ?></button></form>
        </div>
        <?php if (!$stats['invoices'] && !$stats['quotes'] && !$payments): ?>
          <form method="post" data-confirm="¿Eliminar definitivamente este cliente?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn btn-sm btn-danger">Eliminar cliente</button></form>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
