<?php
require dirname(__DIR__) . '/app/bootstrap.php';

$next = (string) input('next');
if (current_admin()) {
    redirect(safe_next($next, 'admin/index.php'));
}

$error = null;
$email = '';
if (is_post()) {
    verify_csrf();
    $email = (string) input('email');
    [$admin, $error] = attempt_login('admin', $email, (string) ($_POST['password'] ?? ''));
    if ($admin) {
        start_user_session('admin', (int) $admin['id']);
        log_activity('admin_login', 'admin', (int) $admin['id']);
        redirect(safe_next($next, 'admin/index.php'));
    }
}

$title = 'Administración';
$area = 'admin';
require APP_ROOT . '/app/views/auth_start.php';
?>
<h1>Administración</h1>
<p>Acceso exclusivo para el equipo de <?= e(setting('company_name', 'Veyon Control')) ?>.</p>
<?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
<form method="post" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="next" value="<?= e($next) ?>">
  <div class="field"><label for="email">Correo</label><input id="email" name="email" type="email" required autocomplete="username" value="<?= e($email) ?>" autofocus></div>
  <div class="field"><label for="password">Contraseña</label><input id="password" name="password" type="password" required autocomplete="current-password"></div>
  <button class="btn btn-primary btn-lg btn-block" type="submit">Ingresar</button>
</form>
<p class="alt"><a class="link" href="<?= e(url('index.php')) ?>">← Volver al sitio</a></p>
<?php require APP_ROOT . '/app/views/auth_end.php'; ?>
