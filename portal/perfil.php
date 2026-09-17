<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$client = require_client();

$fields = ['institution' => 190, 'tax_id' => 40, 'contact_name' => 150, 'position' => 120, 'phone' => 40, 'address' => 255, 'city' => 100, 'country' => 80];
$errors = [];

if (is_post()) {
    verify_csrf();
    if (input('action') === 'profile') {
        $data = [];
        foreach ($fields as $f => $max) {
            $data[$f] = mb_substr((string) input($f), 0, $max);
        }
        if ($data['institution'] === '') {
            $errors[] = 'La institución es obligatoria.';
        }
        if (!$errors) {
            db_update('clients', $data, 'id = ?', [$client['id']]);
            log_activity('client_profile_updated', 'client', (int) $client['id']);
            flash('ok', 'Datos actualizados.');
            redirect('portal/perfil.php');
        }
        $client = array_merge($client, $data);
    } elseif (input('action') === 'password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['password'] ?? '');
        if (!password_verify($current, (string) $client['password_hash'])) {
            $errors[] = 'La contraseña actual no es correcta.';
        } elseif ($p = password_problem($new, (string) ($_POST['password_confirm'] ?? ''))) {
            $errors[] = $p;
        } else {
            db_update('clients', ['password_hash' => password_hash($new, PASSWORD_DEFAULT)], 'id = ?', [$client['id']]);
            session_regenerate_id(true);
            log_activity('client_password_changed', 'client', (int) $client['id']);
            flash('ok', 'Contraseña actualizada.');
            redirect('portal/perfil.php');
        }
    }
}

$title = 'Mis datos';
$area = 'portal';
$nav = 'perfil';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head"><div><h1>Mis datos</h1><p>Esta información aparece en tus facturas y cotizaciones.</p></div></div>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
<div class="grid g-side">
  <form class="card" method="post">
    <div class="card-head"><h2>Institución y contacto</h2></div>
    <div class="card-body">
      <?= csrf_field() ?><input type="hidden" name="action" value="profile">
      <div class="form-grid">
        <div class="field full"><label for="institution">Institución <span class="req">*</span></label><input id="institution" name="institution" required value="<?= e($client['institution']) ?>"></div>
        <div class="field"><label for="tax_id">NIT</label><input id="tax_id" name="tax_id" value="<?= e($client['tax_id']) ?>"></div>
        <div class="field"><label>Correo (usuario)</label><input value="<?= e($client['email']) ?>" readonly><div class="hint">Para cambiarlo, contáctanos.</div></div>
        <div class="field"><label for="contact_name">Contacto</label><input id="contact_name" name="contact_name" value="<?= e($client['contact_name']) ?>"></div>
        <div class="field"><label for="position">Cargo</label><input id="position" name="position" value="<?= e($client['position']) ?>"></div>
        <div class="field"><label for="phone">Teléfono</label><input id="phone" name="phone" value="<?= e($client['phone']) ?>"></div>
        <div class="field"><label for="city">Ciudad</label><input id="city" name="city" value="<?= e($client['city']) ?>"></div>
        <div class="field"><label for="address">Dirección</label><input id="address" name="address" value="<?= e($client['address']) ?>"></div>
        <div class="field"><label for="country">País</label><input id="country" name="country" value="<?= e($client['country']) ?>"></div>
      </div>
      <button class="btn btn-primary" type="submit">Guardar cambios</button>
    </div>
  </form>

  <form class="card" method="post">
    <div class="card-head"><h2>Cambiar contraseña</h2></div>
    <div class="card-body">
      <?= csrf_field() ?><input type="hidden" name="action" value="password">
      <div class="field"><label for="current_password">Contraseña actual</label><input id="current_password" name="current_password" type="password" required autocomplete="current-password"></div>
      <div class="field"><label for="password">Nueva contraseña</label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"></div>
      <div class="field"><label for="password_confirm">Confirmar</label><input id="password_confirm" name="password_confirm" type="password" required autocomplete="new-password"></div>
      <button class="btn" type="submit">Actualizar contraseña</button>
    </div>
  </form>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
