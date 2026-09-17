<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

if (is_post()) {
    verify_csrf();
    $lid = input_int('id');
    $lead = db_one('SELECT * FROM leads WHERE id = ?', [$lid]);
    if ($lead) {
        $action = (string) input('action');
        if (in_array($action, ['new', 'contacted', 'converted', 'closed'], true)) {
            db_update('leads', ['status' => $action], 'id = ?', [$lid]);
            log_activity('lead_status', 'lead', $lid, $action);
            flash('ok', 'Solicitud actualizada.');
        } elseif ($action === 'to_client') {
            $existing = db_val('SELECT id FROM clients WHERE email = ?', [$lead['email']]);
            if ($existing) {
                db_update('leads', ['client_id' => $existing, 'status' => 'converted'], 'id = ?', [$lid]);
                flash('ok', 'Ya existía un cliente con ese correo; la solicitud quedó vinculada.');
                redirect('admin/cliente.php?id=' . $existing);
            }
            redirect('admin/cliente_form.php?lead=' . $lid);
        } elseif ($action === 'delete') {
            db_exec('DELETE FROM leads WHERE id = ?', [$lid]);
            log_activity('lead_deleted', 'lead', $lid, $lead['institution']);
            flash('ok', 'Solicitud eliminada.');
        }
    }
    redirect('admin/solicitudes.php' . (input('back') ? '?estado=' . rawurlencode((string) input('back')) : ''));
}

$estado = (string) input('estado', 'new');
$where = in_array($estado, ['new', 'contacted', 'converted', 'closed'], true) ? 'status = ?' : '1=1';
$params = $where === '1=1' ? [] : [$estado];
$p = paginate((int) db_val("SELECT COUNT(*) FROM leads WHERE {$where}", $params), 20);
$leads = db_all("SELECT * FROM leads WHERE {$where} ORDER BY created_at DESC LIMIT {$p['limit']} OFFSET {$p['offset']}", $params);
$open = input_int('id');

$title = 'Solicitudes';
$area = 'admin';
$nav = 'solicitudes';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head"><div><h1>Solicitudes de cotización</h1><p>Recibidas desde el formulario del sitio web.</p></div></div>
<div class="card">
  <nav class="tabs">
    <?php foreach (['new' => 'Nuevas', 'contacted' => 'Contactadas', 'converted' => 'Convertidas', 'closed' => 'Cerradas', 'todas' => 'Todas'] as $k => $label): ?>
      <a href="?estado=<?= e($k) ?>"<?= $estado === $k ? ' class="on"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <?php if (!$leads): ?>
    <div class="empty">No hay solicitudes en esta vista.</div>
  <?php endif; ?>
  <?php foreach ($leads as $l): ?>
    <details class="list-row" style="display:block"<?= $open === (int) $l['id'] ? ' open' : '' ?>>
      <summary style="display:flex;justify-content:space-between;gap:1rem;cursor:pointer;list-style:none;flex-wrap:wrap">
        <div><div class="t"><?= e($l['institution']) ?> <?= badge(lead_status($l['status'])) ?></div><div class="s"><?= e($l['contact_name']) ?> · <?= e($l['email']) ?> · <?= e($l['equipment']) ?> · <?= e(fdate($l['created_at'], true)) ?></div></div>
        <span class="small link">Ver detalle</span>
      </summary>
      <div class="grid g-2 mt-2">
        <dl class="dl">
          <dt>Contacto</dt><dd><?= e($l['contact_name']) ?><?= $l['position'] ? ' · ' . e($l['position']) : '' ?></dd>
          <dt>Correo</dt><dd><a class="link" href="mailto:<?= e($l['email']) ?>?subject=<?= rawurlencode('Propuesta Veyon para ' . $l['institution']) ?>"><?= e($l['email']) ?></a></dd>
          <dt>Teléfono</dt><dd><?= e($l['phone'] ?: '—') ?></dd>
          <dt>Equipos</dt><dd><?= e($l['equipment']) ?></dd>
          <dt>Sistema</dt><dd><?= e($l['os'] ?: '—') ?></dd>
        </dl>
        <div>
          <div class="label">Mensaje</div>
          <p class="small" style="white-space:pre-line"><?= e($l['message'] ?: '—') ?></p>
        </div>
      </div>
      <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-top:1rem">
        <?php if ($l['client_id']): ?>
          <a class="btn btn-sm btn-primary" href="<?= e(url('admin/cotizacion_form.php?client_id=' . $l['client_id'])) ?>">Crear cotización</a>
          <a class="btn btn-sm" href="<?= e(url('admin/cliente.php?id=' . $l['client_id'])) ?>">Ver cliente</a>
        <?php else: ?>
          <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $l['id'] ?>"><input type="hidden" name="action" value="to_client"><button class="btn btn-sm btn-primary">Convertir en cliente</button></form>
        <?php endif; ?>
        <?php foreach (['contacted' => 'Marcar contactada', 'closed' => 'Cerrar', 'new' => 'Marcar nueva'] as $act => $label): if ($l['status'] === $act) { continue; } ?>
          <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $l['id'] ?>"><input type="hidden" name="back" value="<?= e($estado) ?>"><input type="hidden" name="action" value="<?= $act ?>"><button class="btn btn-sm"><?= e($label) ?></button></form>
        <?php endforeach; ?>
        <form method="post" class="inline" data-confirm="¿Eliminar esta solicitud?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $l['id'] ?>"><input type="hidden" name="action" value="delete"><button class="btn btn-sm btn-danger">Eliminar</button></form>
      </div>
    </details>
  <?php endforeach; ?>
  <?= pagination_links($p) ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
