<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$admin = require_admin();

$id = input_int('id');
$doc = db_one('SELECT * FROM quotes WHERE id = ?', [$id]);
if (!$doc) {
    flash('error', 'Cotización no encontrada.');
    redirect('admin/cotizaciones.php');
}

if (is_post()) {
    verify_csrf();
    $action = (string) input('action');
    $statusMap = ['mark_sent' => 'sent', 'mark_accepted' => 'accepted', 'mark_rejected' => 'rejected', 'mark_draft' => 'draft'];
    if (isset($statusMap[$action]) && $doc['status'] !== 'invoiced') {
        db_update('quotes', ['status' => $statusMap[$action]], 'id = ?', [$id]);
        log_activity('quote_status', 'quote', $id, "{$doc['status']} → {$statusMap[$action]}");
        flash('ok', 'Estado actualizado.');
    } elseif ($action === 'invoice') {
        $invoiceId = convert_quote_to_invoice($id, (int) $admin['id']);
        flash('ok', 'Factura generada desde la cotización.');
        redirect('admin/factura.php?id=' . $invoiceId);
    } elseif ($action === 'delete' && in_array($doc['status'], ['draft', 'rejected', 'sent'], true) && !$doc['invoice_id']) {
        db_exec('DELETE FROM quotes WHERE id = ?', [$id]);
        log_activity('quote_deleted', 'quote', $id, $doc['number']);
        flash('ok', 'Cotización eliminada.');
        redirect('admin/cotizaciones.php');
    }
    redirect('admin/cotizacion.php?id=' . $id);
}

$client = db_one('SELECT * FROM clients WHERE id = ?', [$doc['client_id']]);
$items = document_items('quote', $id);
$payments = [];
$docType = 'quote';

$title = 'Cotización ' . $doc['number'];
$area = 'admin';
$nav = 'cotizaciones';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head no-print">
  <div>
    <div class="crumbs"><a href="<?= e(url('admin/cotizaciones.php')) ?>">Cotizaciones</a> / <a href="<?= e(url('admin/cliente.php?id=' . $client['id'])) ?>"><?= e($client['institution']) ?></a></div>
    <h1>Cotización <?= e($doc['number']) ?> <?= badge(quote_status($doc)) ?></h1>
  </div>
  <div class="actions">
    <button class="btn" type="button" onclick="window.print()">Imprimir / PDF</button>
    <?php if (!in_array($doc['status'], ['invoiced', 'accepted'], true)): ?><a class="btn" href="<?= e(url('admin/cotizacion_form.php?id=' . $id)) ?>">Editar</a><?php endif; ?>
    <a class="btn" href="<?= e(url('admin/cotizacion_form.php?from=' . $id)) ?>">Duplicar</a>
    <?php if ($doc['invoice_id']): ?>
      <a class="btn btn-primary" href="<?= e(url('admin/factura.php?id=' . $doc['invoice_id'])) ?>">Ver factura</a>
    <?php else: ?>
      <form method="post" class="inline" data-confirm="¿Generar la factura a partir de esta cotización?"><?= csrf_field() ?><input type="hidden" name="action" value="invoice"><button class="btn btn-primary">Convertir en factura</button></form>
    <?php endif; ?>
  </div>
</div>

<div class="grid g-side">
  <div><?php require APP_ROOT . '/app/views/document.php'; ?></div>
  <div class="stack no-print">
    <div class="card">
      <div class="card-head"><h3>Estado</h3></div>
      <div class="card-body stack">
        <?php if ($doc['status'] !== 'invoiced'): ?>
          <?php if ($doc['status'] === 'draft'): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="mark_sent"><button class="btn btn-primary btn-block">Marcar como enviada (visible al cliente)</button></form>
          <?php endif; ?>
          <?php if ($doc['status'] !== 'accepted'): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="mark_accepted"><button class="btn btn-block">Marcar aceptada</button></form>
          <?php endif; ?>
          <?php if ($doc['status'] !== 'rejected'): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="mark_rejected"><button class="btn btn-block">Marcar rechazada</button></form>
          <?php endif; ?>
          <?php if ($doc['status'] !== 'draft'): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="mark_draft"><button class="btn btn-ghost btn-block">Volver a borrador</button></form>
          <?php endif; ?>
        <?php else: ?>
          <p class="small muted">Esta cotización ya fue facturada.</p>
        <?php endif; ?>
        <p class="small muted">El cliente puede aceptar o rechazar las cotizaciones enviadas desde su portal; al aceptarlas se genera la factura automáticamente.</p>
        <?php if (!$doc['invoice_id'] && $doc['status'] !== 'accepted'): ?>
          <form method="post" data-confirm="¿Eliminar la cotización <?= e($doc['number']) ?>?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn btn-danger btn-sm">Eliminar</button></form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
