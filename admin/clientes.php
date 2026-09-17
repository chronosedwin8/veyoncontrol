<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$q = (string) input('q');
$status = (string) input('estado');
$where = '1=1';
$params = [];
if ($q !== '') {
    $where .= ' AND (c.institution LIKE ? OR c.email LIKE ? OR c.contact_name LIKE ? OR c.tax_id LIKE ?)';
    $like = '%' . addcslashes($q, '%_\\') . '%';
    array_push($params, $like, $like, $like, $like);
}
if (in_array($status, ['active', 'inactive'], true)) {
    $where .= ' AND c.status = ?';
    $params[] = $status;
}

if (input('export') === 'csv') {
    $rows = db_all("SELECT c.institution, c.tax_id, c.contact_name, c.position, c.email, c.phone, c.city, c.status, c.created_at FROM clients c WHERE {$where} ORDER BY c.institution", $params);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="clientes-' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Institución', 'NIT', 'Contacto', 'Cargo', 'Correo', 'Teléfono', 'Ciudad', 'Estado', 'Creado'], ';');
    foreach ($rows as $r) {
        fputcsv($out, $r, ';');
    }
    exit;
}

$p = paginate((int) db_val("SELECT COUNT(*) FROM clients c WHERE {$where}", $params), 25);
$clients = db_all(
    "SELECT c.*,
       (SELECT COALESCE(SUM(total - amount_paid),0) FROM invoices WHERE client_id = c.id AND status IN ('pending','partial')) AS balance,
       (SELECT COALESCE(SUM(amount),0) FROM payments WHERE client_id = c.id AND status = 'approved') AS paid,
       (SELECT MAX(end_date) FROM licenses WHERE client_id = c.id AND status = 'active') AS license_end
     FROM clients c WHERE {$where} ORDER BY c.institution LIMIT {$p['limit']} OFFSET {$p['offset']}",
    $params
);

$title = 'Clientes';
$area = 'admin';
$nav = 'clientes';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head">
  <div><h1>Clientes</h1><p><?= (int) $p['total'] ?> cliente(s)</p></div>
  <div class="actions">
    <a class="btn" href="?<?= e(http_build_query(array_merge($_GET, ['export' => 'csv']))) ?>">Exportar CSV</a>
    <a class="btn btn-primary" href="<?= e(url('admin/cliente_form.php')) ?>">Nuevo cliente</a>
  </div>
</div>
<div class="card">
  <form class="filters" method="get">
    <input class="input" type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar por institución, correo, contacto o NIT">
    <select class="input" name="estado" data-autosubmit>
      <option value="">Todos los estados</option>
      <option value="active"<?= $status === 'active' ? ' selected' : '' ?>>Activos</option>
      <option value="inactive"<?= $status === 'inactive' ? ' selected' : '' ?>>Inactivos</option>
    </select>
    <button class="btn">Buscar</button>
  </form>
  <?php if (!$clients): ?>
    <div class="empty">No se encontraron clientes.</div>
  <?php else: ?>
  <div class="table-wrap"><table class="tbl">
    <thead><tr><th>Institución</th><th>Contacto</th><th>Licencia hasta</th><th>Portal</th><th class="num">Pagado</th><th class="num">Saldo</th></tr></thead>
    <tbody>
    <?php foreach ($clients as $c): ?>
      <tr>
        <td><a class="row-link" href="<?= e(url('admin/cliente.php?id=' . $c['id'])) ?>"><?= e($c['institution']) ?></a><?php if ($c['status'] === 'inactive'): ?> <span class="pill pill-muted">Inactivo</span><?php endif; ?><br><span class="small muted"><?= e($c['tax_id'] ? 'NIT ' . $c['tax_id'] : '') ?></span></td>
        <td><?= e($c['contact_name']) ?><br><span class="small muted"><?= e($c['email']) ?></span></td>
        <td><?php if ($c['license_end']): ?><?= badge($c['license_end'] >= date('Y-m-d') ? [fdate($c['license_end']), 'ok'] : [fdate($c['license_end']), 'danger']) ?><?php else: ?><span class="muted">—</span><?php endif; ?></td>
        <td><?= $c['password_hash'] ? '<span class="pill pill-ok">Con acceso</span>' : '<span class="pill pill-muted">Sin acceso</span>' ?></td>
        <td class="num"><?= e(money($c['paid'])) ?></td>
        <td class="num"><?= (float) $c['balance'] > 0 ? '<b>' . e(money($c['balance'])) . '</b>' : '<span class="muted">—</span>' ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?= pagination_links($p) ?>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
