<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$keys = [
    'company_name' => 120, 'company_tax_id' => 40, 'company_address' => 190, 'company_city' => 120,
    'company_email' => 190, 'company_phone' => 40, 'notify_email' => 190,
    'tax_label' => 20, 'invoice_prefix' => 10, 'quote_prefix' => 10, 'invoice_footer' => 500,
];
$errors = [];
$mpTest = null;

if (is_post()) {
    verify_csrf();
    if (input('action') === 'mp_test') {
        try {
            [$status, $res] = mp_request('GET', '/users/me');
            $mpTest = $status === 200
                ? ['ok', 'Conexión correcta con la cuenta «' . ($res['nickname'] ?? '') . '» (' . ($res['site_id'] ?? '') . ', ID ' . ($res['id'] ?? '') . ').']
                : ['error', 'Mercado Pago respondió HTTP ' . $status . ': ' . ($res['message'] ?? 'credenciales inválidas')];
        } catch (Throwable $e) {
            $mpTest = ['error', $e->getMessage()];
        }
    } else {
        foreach ($keys as $k => $max) {
            $val = mb_substr((string) input($k), 0, $max);
            if (in_array($k, ['company_email', 'notify_email'], true) && $val !== '' && !valid_email($val)) {
                $errors[] = "El correo «{$val}» no es válido.";
                continue;
            }
            save_setting($k, $val);
        }
        $tax = parse_money(input('tax_rate'));
        $due = input_int('invoice_due_days', 15);
        $valid = input_int('quote_valid_days', 30);
        $months = input_int('license_months', 12);
        if ($tax < 0 || $tax > 100) {
            $errors[] = 'El impuesto debe estar entre 0 y 100 %.';
        } else {
            save_setting('tax_rate', num_input($tax));
        }
        save_setting('invoice_due_days', (string) max(0, min(365, $due)));
        save_setting('quote_valid_days', (string) max(1, min(365, $valid)));
        save_setting('license_months', (string) max(1, min(60, $months)));
        if (!$errors) {
            log_activity('settings_updated', 'settings');
            flash('ok', 'Configuración guardada.');
            redirect('admin/ajustes.php');
        }
    }
}

$s = settings();
$webhookUrl = app_url() . '/api/mp_webhook.php';
$isLocal = is_local_url(app_url()) || !str_starts_with(app_url(), 'https://');
$lastEvents = db_all("SELECT * FROM webhook_events ORDER BY id DESC LIMIT 8");

$title = 'Configuración';
$area = 'admin';
$nav = 'ajustes';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head"><div><h1>Configuración</h1><p>Datos de la empresa, facturación y pasarela de pagos.</p></div></div>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="grid g-side">
  <form class="card" method="post">
    <?= csrf_field() ?>
    <div class="card-head"><h2>Empresa (aparece en facturas y cotizaciones)</h2></div>
    <div class="card-body form-grid">
      <div class="field"><label>Razón social / nombre</label><input name="company_name" value="<?= e($s['company_name'] ?? '') ?>"></div>
      <div class="field"><label>NIT / Tax ID</label><input name="company_tax_id" value="<?= e($s['company_tax_id'] ?? '') ?>"></div>
      <div class="field"><label>Dirección</label><input name="company_address" value="<?= e($s['company_address'] ?? '') ?>"></div>
      <div class="field"><label>Ciudad / país</label><input name="company_city" value="<?= e($s['company_city'] ?? '') ?>"></div>
      <div class="field"><label>Correo de contacto</label><input name="company_email" type="email" value="<?= e($s['company_email'] ?? '') ?>"></div>
      <div class="field"><label>Teléfono</label><input name="company_phone" value="<?= e($s['company_phone'] ?? '') ?>"></div>
      <div class="field full"><label>Correo que recibe avisos de nuevas solicitudes</label><input name="notify_email" type="email" value="<?= e($s['notify_email'] ?? '') ?>"></div>
    </div>
    <div class="card-head" style="border-top:1px solid var(--stroke)"><h2>Facturación</h2></div>
    <div class="card-body form-grid">
      <div class="field"><label>Impuesto por defecto (%)</label><input name="tax_rate" inputmode="decimal" value="<?= e($s['tax_rate'] ?? '0') ?>"><div class="hint">0 = precios sin impuesto. Se aplica a nuevos documentos y compras en línea.</div></div>
      <div class="field"><label>Nombre del impuesto</label><input name="tax_label" value="<?= e($s['tax_label'] ?? 'IVA') ?>"></div>
      <div class="field"><label>Prefijo de facturas</label><input name="invoice_prefix" value="<?= e($s['invoice_prefix'] ?? 'FV-') ?>"></div>
      <div class="field"><label>Prefijo de cotizaciones</label><input name="quote_prefix" value="<?= e($s['quote_prefix'] ?? 'COT-') ?>"></div>
      <div class="field"><label>Días para vencimiento de facturas</label><input name="invoice_due_days" type="number" min="0" value="<?= e($s['invoice_due_days'] ?? '15') ?>"></div>
      <div class="field"><label>Días de validez de cotizaciones</label><input name="quote_valid_days" type="number" min="1" value="<?= e($s['quote_valid_days'] ?? '30') ?>"></div>
      <div class="field"><label>Meses de vigencia de licencias</label><input name="license_months" type="number" min="1" value="<?= e($s['license_months'] ?? '12') ?>"></div>
      <div class="field full"><label>Pie de página de documentos</label><textarea name="invoice_footer" rows="3"><?= e($s['invoice_footer'] ?? '') ?></textarea></div>
      <div class="full"><button class="btn btn-primary">Guardar configuración</button></div>
    </div>
  </form>

  <div class="stack" id="mercadopago">
    <div class="card">
      <div class="card-head"><h2>Mercado Pago</h2><?= mp_configured() ? (mp_is_test() ? '<span class="pill pill-warn">Modo pruebas</span>' : '<span class="pill pill-ok">Producción</span>') : '<span class="pill pill-danger">Sin configurar</span>' ?></div>
      <div class="card-body">
        <?php if ($mpTest): ?><div class="alert alert-<?= e($mpTest[0]) ?>"><?= e($mpTest[1]) ?></div><?php endif; ?>
        <dl class="dl mb-2">
          <dt>Access token</dt><dd><?= mp_configured() ? '<code class="key">' . e(substr((string) config('mercadopago.access_token'), 0, 12)) . '…</code>' : '—' ?></dd>
          <dt>Public key</dt><dd><?= config('mercadopago.public_key') ? '<code class="key">' . e(substr((string) config('mercadopago.public_key'), 0, 12)) . '…</code>' : '—' ?></dd>
          <dt>Firma webhook</dt><dd><?= config('mercadopago.webhook_secret') ? '<span class="pill pill-ok">Configurada</span>' : '<span class="pill pill-warn">Sin clave</span>' ?></dd>
        </dl>
        <?php if (mp_configured()): ?>
          <form method="post" class="mb-2"><?= csrf_field() ?><input type="hidden" name="action" value="mp_test"><button class="btn btn-block">Probar conexión</button></form>
        <?php endif; ?>
        <p class="small muted">Por seguridad las credenciales no se guardan en la base de datos: se configuran en <code class="key">config/config.local.php</code>.</p>
        <div class="label mt-2">URL del webhook</div>
        <div class="copy-field"><input class="input" id="wh" value="<?= e($webhookUrl) ?>" readonly><button class="btn" type="button" data-copy-target="wh">Copiar</button></div>
        <p class="small muted mt-1">Regístrala en Mercado Pago → Tus integraciones → Webhooks, evento «Pagos».</p>
        <?php if ($isLocal): ?>
          <div class="alert alert-warn mt-2 small">El sitio corre en una URL local o sin HTTPS: Mercado Pago no puede enviar webhooks aquí. Los pagos se confirman igualmente al volver del checkout y con «Sincronizar pagos» en cada factura. En producción define <code>app_url</code> con tu dominio HTTPS.</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-head"><h3>Últimas notificaciones recibidas</h3></div>
      <?php if (!$lastEvents): ?><div class="empty small">Aún no se han recibido webhooks.</div><?php endif; ?>
      <?php foreach ($lastEvents as $ev): ?>
        <div class="list-row">
          <div><div class="t small"><?= e($ev['topic'] ?: '—') ?> · <span class="mono"><?= e($ev['resource_id']) ?></span></div><div class="s"><?= e(fdate($ev['created_at'], true)) ?><?= $ev['error'] ? ' · ' . e($ev['error']) : '' ?></div></div>
          <?= $ev['processed'] ? '<span class="pill pill-ok">OK</span>' : '<span class="pill pill-danger">Error</span>' ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
