<?php
/** Definir contraseña desde enlace (recuperación o invitación del administrador). */
require dirname(__DIR__) . '/app/bootstrap.php';

$token = (string) input('token');
$reset = find_password_reset($token);
$error = null;

if ($reset && is_post()) {
    verify_csrf();
    $password = (string) ($_POST['password'] ?? '');
    $error = password_problem($password, (string) ($_POST['password_confirm'] ?? ''));
    if (!$error) {
        $table = $reset['user_type'] === 'admin' ? 'admins' : 'clients';
        db_transaction(function () use ($table, $reset, $password) {
            $data = ['password_hash' => password_hash($password, PASSWORD_DEFAULT)];
            if ($table === 'admins') {
                $data['must_change_password'] = 0;
            }
            db_update($table, $data, 'id = ?', [$reset['user_id']]);
            db_update('password_resets', ['used_at' => date('Y-m-d H:i:s')], 'id = ?', [$reset['id']]);
        });
        start_user_session($reset['user_type'], (int) $reset['user_id']);
        log_activity('password_set', $reset['user_type'], (int) $reset['user_id']);
        flash('ok', 'Tu contraseña quedó guardada.');
        redirect($reset['user_type'] === 'admin' ? 'admin/index.php' : 'portal/index.php');
    }
}

$title = 'Definir contraseña';
$area = $reset['user_type'] ?? 'portal';
require APP_ROOT . '/app/views/auth_start.php';
?>
<h1>Definir contraseña</h1>
<?php if (!$reset): ?>
  <div class="alert alert-error">El enlace no es válido o ya expiró. Solicita uno nuevo.</div>
  <a class="btn btn-primary btn-block" href="<?= e(url('portal/recuperar.php')) ?>">Solicitar un enlace nuevo</a>
<?php else: ?>
  <p>Crea una contraseña segura para tu cuenta.</p>
  <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="token" value="<?= e($token) ?>">
    <div class="field"><label for="password">Nueva contraseña</label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" autofocus><div class="hint">Mínimo 8 caracteres, con letras y números.</div></div>
    <div class="field"><label for="password_confirm">Confirmar contraseña</label><input id="password_confirm" name="password_confirm" type="password" required autocomplete="new-password"></div>
    <button class="btn btn-primary btn-lg btn-block" type="submit">Guardar y entrar</button>
  </form>
<?php endif; ?>
<?php require APP_ROOT . '/app/views/auth_end.php'; ?>
