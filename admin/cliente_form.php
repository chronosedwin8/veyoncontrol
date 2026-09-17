<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$id = input_int('id');
$client = $id ? db_one('SELECT * FROM clients WHERE id = ?', [$id]) : null;
if ($id && !$client) {
    flash('error', 'Cliente no encontrado.');
    redirect('admin/clientes.php');
}
$fromLead = !$client && input_int('lead') ? db_one('SELECT * FROM leads WHERE id = ?', [input_int('lead')]) : null;

$fields = ['institution' => 190, 'tax_id' => 40, 'contact_name' => 150, 'position' => 120, 'email' => 190, 'phone' => 40, 'address' => 255, 'city' => 100, 'country' => 80, 'notes' => 5000];
$v = $client ?: array_fill_keys(array_keys($fields), '') + ['status' => 'active', 'country' => 'Colombia'];
if ($fromLead) {
    $v = array_merge($v, [
        'institution'  => $fromLead['institution'],
        'contact_name' => $fromLead['contact_name'],
        'position'     => $fromLead['position'],
        'email'        => $fromLead['email'],
        'phone'        => $fromLead['phone'],
        'notes'        => trim("Solicitud web: {$fromLead['equipment']} · {$fromLead['os']}\n{$fromLead['message']}"),
        'country'      => 'Colombia',
    ]);
}
$errors = [];

if (is_post()) {
    verify_csrf();
    foreach ($fields as $f => $max) {
        $v[$f] = mb_substr((string) input($f), 0, $max);
    }
    $v['email'] = mb_strtolower($v['email']);
    $v['status'] = input('status') === 'inactive' ? 'inactive' : 'active';
    if ($v['country'] === '') {
        $v['country'] = 'Colombia';
    }

    if ($v['institution'] === '') {
        $errors[] = 'La institución es obligatoria.';
    }
    if (!valid_email($v['email'])) {
        $errors[] = 'El correo no es válido.';
    } elseif (db_val('SELECT id FROM clients WHERE email = ? AND id <> ?', [$v['email'], $id])) {
        $errors[] = 'Ya existe otro cliente con ese correo.';
    }

    if (!$errors) {
        $data = array_intersect_key($v, $fields) + ['status' => $v['status']];
        if ($client) {
            db_update('clients', $data, 'id = ?', [$id]);
            log_activity('client_updated', 'client', $id, $data['institution']);
            flash('ok', 'Cliente actualizado.');
        } else {
            $id = db_insert('clients', $data);
            log_activity('client_created', 'client', $id, $data['institution']);
            if ($fromLead) {
                db_update('leads', ['client_id' => $id, 'status' => 'converted'], 'id = ?', [$fromLead['id']]);
            }
            flash('ok', 'Cliente creado. Puedes generar el enlace de acceso al portal desde su ficha.');
        }
        redirect('admin/cliente.php?id=' . $id);
    }
}

$title = $client ? 'Editar cliente' : 'Nuevo cliente';
$area = 'admin';
$nav = 'clientes';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head">
  <div>
    <div class="crumbs"><a href="<?= e(url('admin/clientes.php')) ?>">Clientes</a><?= $client ? ' / <a href="' . e(url('admin/cliente.php?id=' . $id)) . '">' . e($client['institution']) . '</a>' : '' ?></div>
    <h1><?= e($title) ?></h1>
  </div>
</div>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
<form class="card" method="post" style="max-width:900px">
  <div class="card-body">
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field full"><label for="institution">Institución <span class="req">*</span></label><input id="institution" name="institution" required value="<?= e($v['institution']) ?>"></div>
      <div class="field"><label for="tax_id">NIT</label><input id="tax_id" name="tax_id" value="<?= e($v['tax_id']) ?>"></div>
      <div class="field"><label for="email">Correo <span class="req">*</span></label><input id="email" name="email" type="email" required value="<?= e($v['email']) ?>"><div class="hint">También es el usuario del portal.</div></div>
      <div class="field"><label for="contact_name">Contacto</label><input id="contact_name" name="contact_name" value="<?= e($v['contact_name']) ?>"></div>
      <div class="field"><label for="position">Cargo</label><input id="position" name="position" value="<?= e($v['position']) ?>"></div>
      <div class="field"><label for="phone">Teléfono</label><input id="phone" name="phone" value="<?= e($v['phone']) ?>"></div>
      <div class="field"><label for="city">Ciudad</label><input id="city" name="city" value="<?= e($v['city']) ?>"></div>
      <div class="field"><label for="address">Dirección</label><input id="address" name="address" value="<?= e($v['address']) ?>"></div>
      <div class="field"><label for="country">País</label><input id="country" name="country" value="<?= e($v['country']) ?>"></div>
      <div class="field"><label for="status">Estado</label>
        <select id="status" name="status"><option value="active">Activo</option><option value="inactive"<?= ($v['status'] ?? '') === 'inactive' ? ' selected' : '' ?>>Inactivo (sin acceso al portal)</option></select>
      </div>
      <div class="field full"><label for="notes">Notas internas</label><textarea id="notes" name="notes" rows="4"><?= e($v['notes']) ?></textarea><div class="hint">No son visibles para el cliente.</div></div>
    </div>
    <div style="display:flex;gap:.5rem">
      <button class="btn btn-primary" type="submit"><?= $client ? 'Guardar cambios' : 'Crear cliente' ?></button>
      <a class="btn btn-ghost" href="<?= e(url($client ? 'admin/cliente.php?id=' . $id : 'admin/clientes.php')) ?>">Cancelar</a>
    </div>
  </div>
</form>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
