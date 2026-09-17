<?php
require dirname(__DIR__) . '/app/bootstrap.php';

$next = (string) input('next');
if (current_client()) {
    redirect(safe_next($next, 'portal/index.php'));
}

$error = null;
$email = '';
if (is_post()) {
    verify_csrf();
    $email = (string) input('email');
    [$client, $error] = attempt_login('client', $email, (string) ($_POST['password'] ?? ''));
    if ($client) {
        start_user_session('client', (int) $client['id']);
        log_activity('client_login', 'client', (int) $client['id']);
        redirect(safe_next($next, 'portal/index.php'));
    }
}

$title = 'Ingresar';
$area = 'portal';
require APP_ROOT . '/app/views/auth_start.php';
?>
<h1>Portal de clientes</h1>
<p>Ingresa para ver tus facturas, pagos y licencias.</p>
<?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
<form method="post" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="next" value="<?= e($next) ?>">
  <div class="field">
    <label for="email">Correo electrónico</label>
    <input id="email" name="email" type="email" required autocomplete="username" value="<?= e($email) ?>" autofocus>
  </div>
  <div class="field">
    <label for="password">Contraseña</label>
    <input id="password" name="password" type="password" required autocomplete="current-password">
  </div>
  <div class="field" style="text-align:right;margin-top:-.4rem"><a class="link small" href="<?= e(url('portal/recuperar.php')) ?>">¿Olvidaste tu contraseña?</a></div>
  <button class="btn btn-primary btn-lg btn-block" type="submit">Ingresar</button>
</form>
<p class="alt">¿Aún no tienes cuenta? <a class="link" href="<?= e(url('portal/registro.php' . ($next ? '?next=' . rawurlencode($next) : ''))) ?>">Crear cuenta</a></p>
<div class="alert alert-info mt-2" style="justify-content:center">¿Eres administrador?&nbsp;<a class="link" href="<?= e(url('admin/login.php')) ?>">Ingresa al panel de administración</a></div>
<p class="alt"><a class="link" href="<?= e(url('index.php')) ?>">← Volver al sitio</a></p>
<?php require APP_ROOT . '/app/views/auth_end.php'; ?>
