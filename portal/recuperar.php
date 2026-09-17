<?php
require dirname(__DIR__) . '/app/bootstrap.php';

$sent = false;
if (is_post()) {
    verify_csrf();
    $email = mb_strtolower((string) input('email'));
    // Respuesta idéntica exista o no la cuenta (no revela correos registrados)
    $sent = true;
    if (valid_email($email) && !login_throttled('reset', $email)) {
        record_login_attempt('reset', $email, false);
        $client = db_one("SELECT id, institution FROM clients WHERE email = ? AND status = 'active'", [$email]);
        if ($client) {
            $token = create_password_reset('client', (int) $client['id'], 2);
            $link = abs_url('portal/restablecer.php?token=' . $token);
            send_mail($email, 'Restablecer contraseña - ' . setting('company_name'), "Hola,\n\nPara definir una nueva contraseña del portal de {$client['institution']} abre este enlace (válido por 2 horas):\n\n{$link}\n\nSi no lo solicitaste, ignora este mensaje.");
            log_activity('password_reset_requested', 'client', (int) $client['id']);
        }
    }
}

$title = 'Recuperar contraseña';
$area = 'portal';
require APP_ROOT . '/app/views/auth_start.php';
?>
<h1>Recuperar contraseña</h1>
<?php if ($sent): ?>
  <div class="alert alert-ok">Si el correo está registrado, recibirás un enlace para definir una nueva contraseña. Si no llega en unos minutos, escríbenos a <?= e(setting('company_email')) ?> y te enviamos un enlace de acceso.</div>
<?php else: ?>
  <p>Escribe el correo de tu cuenta y te enviaremos un enlace.</p>
  <form method="post">
    <?= csrf_field() ?>
    <div class="field"><label for="email">Correo electrónico</label><input id="email" name="email" type="email" required autocomplete="email" autofocus></div>
    <button class="btn btn-primary btn-lg btn-block" type="submit">Enviar enlace</button>
  </form>
<?php endif; ?>
<p class="alt"><a class="link" href="<?= e(url('portal/login.php')) ?>">← Volver a ingresar</a></p>
<?php require APP_ROOT . '/app/views/auth_end.php'; ?>
