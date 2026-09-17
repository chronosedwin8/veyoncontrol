<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$me = require_admin();

$created = null;
if (is_post()) {
    verify_csrf();
    $action = (string) input('action');
    if ($action === 'create') {
        $name = mb_substr((string) input('name'), 0, 120);
        $email = mb_strtolower((string) input('email'));
        if ($name === '' || !valid_email($email)) {
            flash('error', 'Indica nombre y un correo válido.');
        } elseif (db_val('SELECT id FROM admins WHERE email = ?', [$email])) {
            flash('error', 'Ya existe un administrador con ese correo.');
        } else {
            $temp = random_password(14);
            $id = db_insert('admins', ['name' => $name, 'email' => $email, 'password_hash' => password_hash($temp, PASSWORD_DEFAULT), 'must_change_password' => 1]);
            log_activity('admin_created', 'admin', $id, $email);
            $created = ['email' => $email, 'password' => $temp];
        }
    } elseif (in_array($action, ['disable', 'enable', 'reset'], true)) {
        $id = input_int('id');
        if ($id === (int) $me['id']) {
            flash('error', 'No puedes modificar tu propia cuenta desde aquí.');
        } elseif ($action === 'reset') {
            $temp = random_password(14);
            db_update('admins', ['password_hash' => password_hash($temp, PASSWORD_DEFAULT), 'must_change_password' => 1], 'id = ?', [$id]);
            log_activity('admin_password_reset', 'admin', $id);
            $created = ['email' => db_val('SELECT email FROM admins WHERE id = ?', [$id]), 'password' => $temp];
        } else {
            if ($action === 'disable' && (int) db_val('SELECT COUNT(*) FROM admins WHERE active = 1') <= 1) {
                flash('error', 'Debe quedar al menos un administrador activo.');
            } else {
                db_update('admins', ['active' => $action === 'enable' ? 1 : 0], 'id = ?', [$id]);
                log_activity('admin_' . $action, 'admin', $id);
                flash('ok', 'Administrador actualizado.');
            }
        }
    }
    if (!$created) {
        redirect('admin/usuarios.php');
    }
}

$admins = db_all('SELECT * FROM admins ORDER BY active DESC, name');

$title = 'Administradores';
$area = 'admin';
$nav = 'usuarios';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head"><div><h1>Administradores</h1><p>Personas con acceso completo al panel.</p></div></div>
<?php if ($created): ?>
  <div class="alert alert-info" style="display:block">
    <b>Contraseña temporal para <?= e($created['email']) ?></b> (se mostrará solo esta vez; se pedirá cambiarla al ingresar):
    <div class="copy-field mt-1"><input class="input" id="tmp-pass" value="<?= e($created['password']) ?>" readonly><button class="btn" type="button" data-copy-target="tmp-pass">Copiar</button></div>
  </div>
<?php endif; ?>
<div class="grid g-side">
  <div class="card">
    <div class="table-wrap"><table class="tbl">
      <thead><tr><th>Nombre</th><th>Correo</th><th>Último ingreso</th><th>Estado</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($admins as $a): ?>
        <tr>
          <td><b><?= e($a['name']) ?></b><?= (int) $a['id'] === (int) $me['id'] ? ' <span class="pill pill-info">Tú</span>' : '' ?></td>
          <td><?= e($a['email']) ?></td>
          <td class="nowrap"><?= e(fdate($a['last_login_at'], true)) ?></td>
          <td><?= $a['active'] ? ($a['must_change_password'] ? badge(['Contraseña temporal', 'warn']) : badge(['Activo', 'ok'])) : badge(['Desactivado', 'muted']) ?></td>
          <td class="right nowrap">
            <?php if ((int) $a['id'] !== (int) $me['id']): ?>
              <form method="post" class="inline" data-confirm="¿Generar una nueva contraseña temporal?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $a['id'] ?>"><input type="hidden" name="action" value="reset"><button class="btn btn-sm">Restablecer</button></form>
              <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $a['id'] ?>"><input type="hidden" name="action" value="<?= $a['active'] ? 'disable' : 'enable' ?>"><button class="btn btn-sm <?= $a['active'] ? 'btn-danger' : '' ?>"><?= $a['active'] ? 'Desactivar' : 'Activar' ?></button></form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
  <form class="card" method="post">
    <div class="card-head"><h2>Nuevo administrador</h2></div>
    <div class="card-body">
      <?= csrf_field() ?><input type="hidden" name="action" value="create">
      <div class="field"><label>Nombre</label><input name="name" required></div>
      <div class="field"><label>Correo</label><input name="email" type="email" required></div>
      <p class="small muted mb-2">Se generará una contraseña temporal que deberá cambiar en su primer ingreso.</p>
      <button class="btn btn-primary">Crear administrador</button>
    </div>
  </form>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
