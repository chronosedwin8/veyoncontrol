<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$admin = require_admin();
$errors = [];

if (is_post()) {
    verify_csrf();
    $name = mb_substr((string) input('name'), 0, 120);
    $current = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['password'] ?? '');

    if ($name === '') {
        $errors[] = 'El nombre es obligatorio.';
    }
    $changing = $new !== '' || $admin['must_change_password'];
    if ($changing) {
        if (!password_verify($current, $admin['password_hash'])) {
            $errors[] = 'La contraseña actual no es correcta.';
        } elseif ($p = password_problem($new, (string) ($_POST['password_confirm'] ?? ''))) {
            $errors[] = $p;
        } elseif (password_verify($new, $admin['password_hash'])) {
            $errors[] = 'La nueva contraseña debe ser distinta de la actual.';
        }
    }
    if (!$errors) {
        $data = ['name' => $name];
        if ($changing) {
            $data['password_hash'] = password_hash($new, PASSWORD_DEFAULT);
            $data['must_change_password'] = 0;
        }
        db_update('admins', $data, 'id = ?', [$admin['id']]);
        if ($changing) {
            session_regenerate_id(true);
            log_activity('admin_password_changed', 'admin', (int) $admin['id']);
        }
        flash('ok', 'Cuenta actualizada.');
        redirect($changing && $admin['must_change_password'] ? 'admin/index.php' : 'admin/perfil.php');
    }
}

$title = 'Mi cuenta';
$area = 'admin';
$nav = '';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head"><div><h1>Mi cuenta</h1><p><?= e($admin['email']) ?></p></div></div>
<?php if ($admin['must_change_password']): ?><div class="alert alert-warn">Estás usando una contraseña temporal. Define una nueva para continuar.</div><?php endif; ?>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
<form class="card" method="post" style="max-width:560px">
  <div class="card-body">
    <?= csrf_field() ?>
    <div class="field"><label for="name">Nombre</label><input id="name" name="name" required value="<?= e($admin['name']) ?>"></div>
    <div class="field"><label for="current_password">Contraseña actual<?= $admin['must_change_password'] ? ' (temporal)' : '' ?></label><input id="current_password" name="current_password" type="password" autocomplete="current-password" <?= $admin['must_change_password'] ? 'required' : '' ?>></div>
    <div class="form-grid">
      <div class="field"><label for="password">Nueva contraseña</label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" <?= $admin['must_change_password'] ? 'required' : '' ?>></div>
      <div class="field"><label for="password_confirm">Confirmar</label><input id="password_confirm" name="password_confirm" type="password" autocomplete="new-password"></div>
    </div>
    <p class="small muted mb-2">Deja la contraseña en blanco si solo cambias el nombre. Mínimo 8 caracteres con letras y números.</p>
    <button class="btn btn-primary" type="submit">Guardar</button>
  </div>
</form>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
