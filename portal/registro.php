<?php
require dirname(__DIR__) . '/app/bootstrap.php';

$next = (string) input('next');
if (current_client()) {
    redirect(safe_next($next, 'portal/index.php'));
}

$errors = [];
$v = ['institution' => '', 'tax_id' => '', 'contact_name' => '', 'position' => '', 'email' => '', 'phone' => '', 'city' => ''];

if (is_post()) {
    verify_csrf();
    foreach ($v as $k => $_) {
        $v[$k] = mb_substr((string) input($k), 0, $k === 'institution' || $k === 'email' ? 190 : 150);
    }
    $v['email'] = mb_strtolower($v['email']);
    $password = (string) ($_POST['password'] ?? '');

    if ($v['institution'] === '') {
        $errors[] = 'Indica el nombre de la institución.';
    }
    if ($v['contact_name'] === '') {
        $errors[] = 'Indica el nombre de la persona de contacto.';
    }
    if (!valid_email($v['email'])) {
        $errors[] = 'El correo no es válido.';
    }
    if ($p = password_problem($password, (string) ($_POST['password_confirm'] ?? ''))) {
        $errors[] = $p;
    }
    if (empty($_POST['terms'])) {
        $errors[] = 'Debes aceptar el tratamiento de datos para crear la cuenta.';
    }
    if (!empty($_POST['website'])) {
        $errors[] = 'Solicitud no válida.';
    }

    if (!$errors) {
        $existing = db_one('SELECT id, password_hash FROM clients WHERE email = ?', [$v['email']]);
        if ($existing && $existing['password_hash']) {
            $errors[] = 'Ya existe una cuenta con ese correo. Ingresa o recupera tu contraseña.';
        } elseif ($existing) {
            // Cliente creado por el administrador sin acceso: se le asigna contraseña
            // solo mediante el enlace de invitación, nunca por registro abierto.
            $errors[] = 'Tu institución ya está registrada. Usa «¿Olvidaste tu contraseña?» para activar el acceso.';
        } else {
            $id = db_insert('clients', $v + ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
            start_user_session('client', $id);
            log_activity('client_registered', 'client', $id, $v['institution']);
            db_exec("UPDATE leads SET client_id = ? WHERE email = ? AND client_id IS NULL", [$id, $v['email']]);
            flash('ok', '¡Bienvenido! Tu cuenta fue creada.');
            redirect(safe_next($next, 'portal/index.php'));
        }
    }
}

$title = 'Crear cuenta';
$area = 'portal';
require APP_ROOT . '/app/views/auth_start.php';
?>
<h1>Crear cuenta</h1>
<p>Registra tu institución para comprar en línea y administrar tus licencias.</p>
<?php foreach ($errors as $err): ?><div class="alert alert-error" role="alert"><?= e($err) ?></div><?php endforeach; ?>
<form method="post" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="next" value="<?= e($next) ?>">
  <div style="position:absolute;left:-9999px" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
  <div class="form-grid">
    <div class="field full"><label for="institution">Institución <span class="req">*</span></label><input id="institution" name="institution" required value="<?= e($v['institution']) ?>" autocomplete="organization"></div>
    <div class="field"><label for="tax_id">NIT</label><input id="tax_id" name="tax_id" value="<?= e($v['tax_id']) ?>"></div>
    <div class="field"><label for="city">Ciudad</label><input id="city" name="city" value="<?= e($v['city']) ?>" autocomplete="address-level2"></div>
    <div class="field"><label for="contact_name">Contacto <span class="req">*</span></label><input id="contact_name" name="contact_name" required value="<?= e($v['contact_name']) ?>" autocomplete="name"></div>
    <div class="field"><label for="position">Cargo</label><input id="position" name="position" value="<?= e($v['position']) ?>" autocomplete="organization-title"></div>
    <div class="field"><label for="email">Correo <span class="req">*</span></label><input id="email" name="email" type="email" required value="<?= e($v['email']) ?>" autocomplete="email"></div>
    <div class="field"><label for="phone">Teléfono</label><input id="phone" name="phone" type="tel" value="<?= e($v['phone']) ?>" autocomplete="tel"></div>
    <div class="field"><label for="password">Contraseña <span class="req">*</span></label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"><div class="hint">Mínimo 8 caracteres, con letras y números.</div></div>
    <div class="field"><label for="password_confirm">Confirmar <span class="req">*</span></label><input id="password_confirm" name="password_confirm" type="password" required autocomplete="new-password"></div>
    <div class="field full"><label class="check"><input type="checkbox" name="terms" value="1" required> <span>Acepto los <a class="link" href="<?= e(url('terminos.php')) ?>" target="_blank" rel="noopener">términos del servicio</a> y autorizo el tratamiento de mis datos según el <a class="link" href="<?= e(url('privacidad.php')) ?>" target="_blank" rel="noopener">aviso de privacidad</a>.</span></label></div>
  </div>
  <button class="btn btn-primary btn-lg btn-block" type="submit">Crear cuenta</button>
</form>
<p class="alt">¿Ya tienes cuenta? <a class="link" href="<?= e(url('portal/login.php' . ($next ? '?next=' . rawurlencode($next) : ''))) ?>">Ingresar</a></p>
<?php require APP_ROOT . '/app/views/auth_end.php'; ?>
