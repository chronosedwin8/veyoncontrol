<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$p = paginate((int) db_val('SELECT COUNT(*) FROM activity_log'), 50);
$rows = db_all(
    "SELECT a.*, ad.name admin_name, c.institution client_name
     FROM activity_log a
     LEFT JOIN admins ad ON a.actor_type = 'admin' AND ad.id = a.actor_id
     LEFT JOIN clients c ON a.actor_type = 'client' AND c.id = a.actor_id
     ORDER BY a.id DESC LIMIT {$p['limit']} OFFSET {$p['offset']}"
);
$links = [
    'invoice' => 'admin/factura.php?id=', 'quote' => 'admin/cotizacion.php?id=', 'client' => 'admin/cliente.php?id=',
    'lead' => 'admin/solicitudes.php?estado=todas&id=',
];

$title = 'Actividad';
$area = 'admin';
$nav = 'actividad';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head"><div><h1>Registro de actividad</h1><p>Auditoría de acciones en el panel, el portal y los pagos.</p></div></div>
<div class="card">
  <div class="table-wrap"><table class="tbl">
    <thead><tr><th>Fecha</th><th>Quién</th><th>Acción</th><th>Detalle</th><th>IP</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td class="nowrap small"><?= e(fdate($r['created_at'], true)) ?></td>
        <td class="small"><?= e($r['actor_type'] === 'admin' ? ($r['admin_name'] ?? 'Admin') : ($r['actor_type'] === 'client' ? ($r['client_name'] ?? 'Cliente') : 'Sistema')) ?><br><span class="muted"><?= e($r['actor_type']) ?></span></td>
        <td class="mono small">
          <?php if ($r['entity_id'] && isset($links[$r['entity']])): ?><a class="link" href="<?= e(url($links[$r['entity']] . $r['entity_id'])) ?>"><?= e($r['action']) ?></a><?php else: ?><?= e($r['action']) ?><?php endif; ?>
        </td>
        <td class="small"><?= e($r['details']) ?></td>
        <td class="mono small muted"><?= e($r['ip']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?= pagination_links($p) ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
