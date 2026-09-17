<?php
/**
 * Formulario compartido de factura / cotización.
 * @var string $docType 'invoice' | 'quote'
 * @var array  $v       valores del encabezado
 * @var array  $items   líneas
 * @var array  $clients
 * @var array  $plans
 * @var bool   $editing
 */
$isInvoice = $docType === 'invoice';
$catalog = [];
foreach ($plans as $pl) {
    $catalog[$pl['id']] = [
        'desc'  => $pl['name'] . ' (' . $pl['capacity_label'] . ') — licencia anual con actualizaciones, capacitación y soporte',
        'price' => num_input($pl['price_cop']),
    ];
}
$planOptions = function ($selected) use ($plans) {
    $html = '<option value="">Texto libre</option>';
    foreach ($plans as $pl) {
        $html .= '<option value="' . (int) $pl['id'] . '"' . ((int) $selected === (int) $pl['id'] ? ' selected' : '') . '>' . e($pl['name']) . '</option>';
    }
    return $html;
};
?>
<form method="post" class="stack">
  <?= csrf_field() ?>
  <div class="card">
    <div class="card-body form-grid">
      <div class="field full">
        <label for="client_id">Cliente <span class="req">*</span></label>
        <select id="client_id" name="client_id" required>
          <option value="">Selecciona un cliente…</option>
          <?php foreach ($clients as $c): ?>
            <option value="<?= (int) $c['id'] ?>"<?= (int) $v['client_id'] === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['institution']) ?> — <?= e($c['email']) ?></option>
          <?php endforeach; ?>
        </select>
        <div class="hint">¿No está? <a class="link" href="<?= e(url('admin/cliente_form.php')) ?>">Crear cliente</a></div>
      </div>
      <div class="field"><label for="issue_date">Fecha de emisión</label><input id="issue_date" name="issue_date" type="date" required value="<?= e($v['issue_date']) ?>"></div>
      <?php if ($isInvoice): ?>
        <div class="field"><label for="due_date">Fecha de vencimiento</label><input id="due_date" name="due_date" type="date" value="<?= e($v['due_date']) ?>"></div>
        <div class="field"><label for="status">Estado</label>
          <select id="status" name="status">
            <option value="pending"<?= $v['status'] !== 'draft' ? ' selected' : '' ?>>Emitida (visible para el cliente)</option>
            <option value="draft"<?= $v['status'] === 'draft' ? ' selected' : '' ?>>Borrador (oculta al cliente)</option>
          </select>
        </div>
      <?php else: ?>
        <div class="field"><label for="valid_until">Válida hasta</label><input id="valid_until" name="valid_until" type="date" value="<?= e($v['valid_until']) ?>"></div>
        <div class="field"><label for="status">Estado</label>
          <select id="status" name="status">
            <option value="sent"<?= $v['status'] === 'sent' ? ' selected' : '' ?>>Enviada (visible para el cliente)</option>
            <option value="draft"<?= $v['status'] === 'draft' ? ' selected' : '' ?>>Borrador (oculta al cliente)</option>
          </select>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-head"><h2>Conceptos</h2><button class="btn btn-sm" type="button" data-add-row>+ Agregar línea</button></div>
    <div class="table-wrap">
      <table class="tbl items-table" data-items-editor data-catalog="<?= e(json_encode($catalog)) ?>">
        <thead><tr><th class="c-plan">Plan</th><th>Descripción</th><th class="c-qty">Cant.</th><th class="c-price">Valor unitario</th><th class="c-total">Total</th><th class="c-del"></th></tr></thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td class="c-plan"><select class="input" name="item_plan[]" aria-label="Plan"><?= $planOptions($it['plan_id']) ?></select></td>
              <td class="c-desc"><input class="input" name="item_desc[]" value="<?= e($it['description']) ?>" aria-label="Descripción"></td>
              <td class="c-qty"><input class="input" name="item_qty[]" value="<?= e(num_input($it['quantity'])) ?>" inputmode="decimal" aria-label="Cantidad"></td>
              <td class="c-price"><input class="input" name="item_price[]" value="<?= e(num_input($it['unit_price'])) ?>" inputmode="decimal" aria-label="Valor unitario"></td>
              <td class="c-total"></td>
              <td class="c-del"><button class="btn btn-sm btn-ghost" type="button" data-del-row aria-label="Quitar línea">✕</button></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <template id="item-row-template">
      <tr>
        <td class="c-plan"><select class="input" name="item_plan[]" aria-label="Plan"><?= $planOptions(null) ?></select></td>
        <td class="c-desc"><input class="input" name="item_desc[]" aria-label="Descripción" placeholder="Descripción del concepto"></td>
        <td class="c-qty"><input class="input" name="item_qty[]" value="1" inputmode="decimal" aria-label="Cantidad"></td>
        <td class="c-price"><input class="input" name="item_price[]" value="0" inputmode="decimal" aria-label="Valor unitario"></td>
        <td class="c-total"></td>
        <td class="c-del"><button class="btn btn-sm btn-ghost" type="button" data-del-row aria-label="Quitar línea">✕</button></td>
      </tr>
    </template>
    <div class="card-body">
      <div class="grid g-2">
        <div class="field">
          <label for="notes">Observaciones (visibles para el cliente)</label>
          <textarea id="notes" name="notes" rows="4"><?= e($v['notes']) ?></textarea>
          <div class="hint">Al elegir un plan en una línea, se completan la descripción y el precio vigentes. Las facturas pagadas con planes activan la licencia automáticamente.</div>
        </div>
        <div class="totals">
          <div class="row"><span>Subtotal</span><b data-subtotal>$ 0</b></div>
          <div class="row"><label for="discount">Descuento ($)</label><input class="input" id="discount" name="discount" value="<?= e(num_input($v['discount'])) ?>" inputmode="decimal"></div>
          <div class="row"><label for="tax_rate"><?= e(setting('tax_label', 'IVA')) ?> (%)</label><input class="input" id="tax_rate" name="tax_rate" value="<?= e(num_input($v['tax_rate'])) ?>" inputmode="decimal"></div>
          <div class="row"><span>Impuesto</span><b data-tax>$ 0</b></div>
          <div class="row grand"><span>Total</span><span data-total>$ 0</span></div>
        </div>
      </div>
    </div>
  </div>

  <div style="display:flex;gap:.5rem;flex-wrap:wrap">
    <button class="btn btn-primary btn-lg" type="submit"><?= $editing ? 'Guardar cambios' : ($isInvoice ? 'Crear factura' : 'Crear cotización') ?></button>
    <a class="btn btn-lg btn-ghost" href="javascript:history.back()">Cancelar</a>
  </div>
</form>
