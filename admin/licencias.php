<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

if (is_post()) {
    verify_csrf();
    $action = (string) input('action');
    if ($action === 'save') {
        $lid = input_int('id');
        $data = [
            'client_id'   => input_int('client_id'),
            'plan_id'     => input_int('plan_id') ?: null,
            'description' => mb_substr((string) input('description'), 0, 190),
            'start_date'  => (string) input('start_date'),
            'end_date'    => (string) input('end_date'),
            'status'      => input('status') === 'cancelled' ? 'cancelled' : 'active',
            'notes'       => mb_substr((string) input('notes'), 0, 255),
        ];
        if (!db_val('SELECT id FROM clients WHERE id = ?', [$data['client_id']]) || $data['description'] === ''
            || !valid_date($data['start_date']) || !valid_date($data['end_date']) || $data['end_date'] < $data['start_date']) {
            flash('error', 'Revisa los datos: cliente, descripción y fechas válidas (fin posterior al inicio).');
            redirect('admin/licencias.php' . ($lid ? '?edit=' . $lid : '?nueva=1'));
        }
        if ($lid) {
            db_update('licenses', $data, 'id = ?', [$lid]);
            log_activity('license_updated', 'license', $lid, $data['description']);
        } else {
            $lid = db_insert('licenses', $data);
            log_activity('license_created', 'license', $lid, $data['description']);
        }
        flash('ok', 'Licencia guardada.');
    }
    redirect('admin/licencias.php');
}

$filtro = (string) input('filtro');
$where = '1=1';
if ($filtro === 'vigentes') {
    $where = "l.status = 'active' AND l.end_date >= CURDATE()";
} elseif ($filtro === 'por_vencer') {
    $where = "l.status = 'active' AND l.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)";
} elseif ($filtro === 'vencidas') {
    $where = "l.status = 'active' AND l.end_date < CURDATE()";
}
$licenses = db_all("SELECT l.*, c.institution, c.email FROM licenses l JOIN clients c ON c.id = l.client_id WHERE {$where} ORDER BY l.end_date ASC LIMIT 500");

$edit = null;
if (input_int('edit')) {
    $edit = db_one('SELECT * FROM licenses WHERE id = ?', [input_int('edit')]);
} elseif (input('nueva')) {
    $months = (int) setting('license_months', '12');
    $edit = ['id' => 0, 'client_id' => input_int('client_id'), 'plan_id' => null, 'description' => '', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime("+{$months} months -1 day")), 'status' => 'active', 'notes' => ''];
}

$title = 'Licencias';
$area = 'admin';
$nav = 'licencias';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head">
  <div><h1>Licencias</h1><p>Se crean automáticamente al pagarse una factura con planes. También puedes registrarlas a mano.</p></div>
  <div class="actions"><a class="btn btn-primary" href="?nueva=1">Nueva licencia manual</a></div>
</div>

<?php if ($edit): ?>
<form class="card mb-3" method="post">
  <div class="card-head"><h2><?= $edit['id'] ? 'Editar licencia' : 'Nueva licencia' ?></h2><a class="btn btn-sm btn-ghost" href="<?= e(url('admin/licencias.php')) ?>">Cerrar</a></div>
  <div class="card-body">
    <?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>">
    <div class="form-grid">
      <div class="field"><label>Cliente</label><select name="client_id" required><option value="">Selecciona…</option><?php foreach (db_all('SELECT id, institution FROM clients ORDER BY institution') as $c): ?><option value="<?= (int) $c['id'] ?>"<?= (int) $edit['client_id'] === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['institution']) ?></option><?php endforeach; ?></select></div>
      <div class="field"><label>Plan</label><select name="plan_id"><option value="">—</option><?php foreach (plans_catalog() as $pl): ?><option value="<?= (int) $pl['id'] ?>"<?= (int) $edit['plan_id'] === (int) $pl['id'] ? ' selected' : '' ?>><?= e($pl['name']) ?></option><?php endforeach; ?></select></div>
      <div class="field full"><label>Descripción</label><input name="description" required value="<?= e($edit['description']) ?>" placeholder="Licencia Laboratorio · Hasta 50 equipos"></div>
      <div class="field"><label>Inicio</label><input type="date" name="start_date" required value="<?= e($edit['start_date']) ?>"></div>
      <div class="field"><label>Fin</label><input type="date" name="end_date" required value="<?= e($edit['end_date']) ?>"></div>
      <div class="field"><label>Estado</label><select name="status"><option value="active">Activa</option><option value="cancelled"<?= $edit['status'] === 'cancelled' ? ' selected' : '' ?>>Cancelada</option></select></div>
      <div class="field"><label>Notas</label><input name="notes" value="<?= e($edit['notes']) ?>"></div>
    </div>
    <button class="btn btn-primary">Guardar licencia</button>
  </div>
</form>
<?php endif; ?>

<div class="card">
  <nav class="tabs">
    <?php foreach (['' => 'Todas', 'vigentes' => 'Vigentes', 'por_vencer' => 'Por vencer (60 días)', 'vencidas' => 'Vencidas'] as $k => $label): ?>
      <a href="?filtro=<?= e($k) ?>"<?= $filtro === $k ? ' class="on"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <?php if (!$licenses): ?>
    <div class="empty">No hay licencias en esta vista.</div>
  <?php else: ?>
  <div class="table-wrap"><table class="tbl">
    <thead><tr><th>Cliente</th><th>Licencia</th><th>Inicio</th><th>Fin</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($licenses as $l):
        $days = (int) floor((strtotime($l['end_date']) - strtotime('today')) / 86400);
        $st = $l['status'] === 'cancelled' ? ['Cancelada', 'muted'] : ($days < 0 ? ['Vencida', 'danger'] : ($days <= 30 ? ["Vence en {$days} d", 'warn'] : ['Vigente', 'ok']));
      ?>
      <tr>
        <td><a class="row-link" href="<?= e(url('admin/cliente.php?id=' . $l['client_id'] . '&tab=licencias')) ?>"><?= e($l['institution']) ?></a><br><span class="small muted"><?= e($l['email']) ?></span></td>
        <td><?= e($l['description']) ?><?= $l['invoice_id'] ? '<br><a class="small link" href="' . e(url('admin/factura.php?id=' . $l['invoice_id'])) . '">Ver factura</a>' : '' ?></td>
        <td class="nowrap"><?= e(fdate($l['start_date'])) ?></td>
        <td class="nowrap"><?= e(fdate($l['end_date'])) ?></td>
        <td><?= badge($st) ?></td>
        <td class="right nowrap">
          <a class="btn btn-sm" href="?edit=<?= (int) $l['id'] ?>">Editar</a>
          <?php if ($l['status'] === 'active' && $days <= 60): ?><a class="btn btn-sm btn-primary" href="<?= e(url('admin/cotizacion_form.php?client_id=' . $l['client_id'])) ?>">Cotizar renovación</a><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
