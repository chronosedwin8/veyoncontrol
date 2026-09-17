<?php
/** Administración de los precios y planes que muestra el sitio público. */
require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$errors = [];
if (is_post()) {
    verify_csrf();
    $section = (string) input('section');

    if ($section === 'plan') {
        $pid = input_int('id');
        $data = [
            'name'            => mb_substr((string) input('name'), 0, 120),
            'slug'            => trim(preg_replace('/[^a-z0-9]+/', '-', mb_strtolower((string) (input('slug') ?: input('name')))), '-'),
            'capacity_label'  => mb_substr((string) input('capacity_label'), 0, 80),
            'subtitle'        => mb_substr((string) input('subtitle'), 0, 255),
            'price_cop'       => parse_money(input('price_cop')),
            'period_label'    => mb_substr((string) input('period_label'), 0, 80),
            'features'        => implode("\n", lines((string) input('features'))),
            'compare_values'  => (string) input('compare_values'),
            'ribbon'          => mb_substr((string) input('ribbon'), 0, 40) ?: null,
            'featured'        => input('featured') ? 1 : 0,
            'online_purchase' => input('online_purchase') ? 1 : 0,
            'active'          => input('active') ? 1 : 0,
            'sort_order'      => input_int('sort_order'),
        ];
        if ($data['name'] === '' || $data['slug'] === '') {
            $errors[] = 'El plan necesita un nombre.';
        } elseif (db_val('SELECT id FROM plans WHERE slug = ? AND id <> ?', [$data['slug'], $pid])) {
            $errors[] = 'Ya existe otro plan con el identificador «' . $data['slug'] . '».';
        }
        if (!$errors) {
            db_transaction(function () use (&$pid, $data) {
                if ($data['featured']) {
                    db_exec('UPDATE plans SET featured = 0 WHERE id <> ?', [$pid]);
                }
                if ($pid) {
                    $old = db_one('SELECT price_cop FROM plans WHERE id = ?', [$pid]);
                    db_update('plans', $data, 'id = ?', [$pid]);
                    log_activity('plan_updated', 'plan', $pid, $data['name'] . ' ' . money($old['price_cop']) . ' → ' . money($data['price_cop']));
                } else {
                    $pid = db_insert('plans', $data);
                    log_activity('plan_created', 'plan', $pid, $data['name']);
                }
            });
            flash('ok', 'Plan «' . $data['name'] . '» guardado. Los cambios ya se ven en el sitio.');
            redirect('admin/precios.php#plan-' . $pid);
        }
    } elseif ($section === 'plan_delete') {
        $pid = input_int('id');
        if (db_val('SELECT COUNT(*) FROM invoice_items WHERE plan_id = ?', [$pid]) || db_val('SELECT COUNT(*) FROM quote_items WHERE plan_id = ?', [$pid])) {
            db_update('plans', ['active' => 0], 'id = ?', [$pid]);
            flash('warn', 'El plan tiene documentos asociados: se ocultó del sitio en lugar de eliminarlo.');
        } else {
            db_exec('DELETE FROM plans WHERE id = ?', [$pid]);
            flash('ok', 'Plan eliminado.');
        }
        log_activity('plan_deleted', 'plan', $pid);
        redirect('admin/precios.php');
    } elseif ($section === 'addons') {
        db_transaction(function () {
            $ids = (array) ($_POST['addon_id'] ?? []);
            foreach ($ids as $i => $aid) {
                $org = mb_substr(trim((string) ($_POST['addon_org'][$i] ?? '')), 0, 150);
                $delete = !empty($_POST['addon_delete'][$aid]);
                if ($delete && $aid) {
                    db_exec('DELETE FROM addon_prices WHERE id = ?', [(int) $aid]);
                    continue;
                }
                if ($org === '') {
                    continue;
                }
                $single = trim((string) ($_POST['addon_single'][$i] ?? ''));
                $bundle = trim((string) ($_POST['addon_bundle'][$i] ?? ''));
                $data = [
                    'org_type'     => $org,
                    'single_price' => $single === '' ? null : parse_money($single),
                    'bundle_price' => $bundle === '' ? null : parse_money($bundle),
                    'includes'     => mb_substr(trim((string) ($_POST['addon_includes'][$i] ?? '')), 0, 190),
                    'custom_quote' => !empty($_POST['addon_custom'][$i]) ? 1 : 0,
                    'sort_order'   => $i,
                ];
                if ((int) $aid) {
                    db_update('addon_prices', $data, 'id = ?', [(int) $aid]);
                } else {
                    db_insert('addon_prices', $data);
                }
            }
        });
        log_activity('addon_prices_updated', 'settings');
        flash('ok', 'Precios de complementos actualizados.');
        redirect('admin/precios.php#complementos');
    } elseif ($section === 'general') {
        $rate = parse_money(input('eur_rate'));
        if ($rate <= 0) {
            $errors[] = 'La tasa COP por EUR debe ser mayor que cero.';
        } else {
            save_setting('eur_rate', num_input($rate));
            save_setting('hero_version', mb_substr((string) input('hero_version'), 0, 20));
            save_setting('compare_labels', implode("\n", lines((string) input('compare_labels'))));
            log_activity('home_settings_updated', 'settings');
            flash('ok', 'Ajustes del home guardados.');
            redirect('admin/precios.php#general');
        }
    }
}

$plans = plans_catalog();
$addons = db_all('SELECT * FROM addon_prices ORDER BY sort_order, id');
$labels = lines(setting('compare_labels'));
$newPlan = ['id' => 0, 'name' => '', 'slug' => '', 'capacity_label' => '', 'subtitle' => '', 'price_cop' => 0, 'period_label' => 'por año', 'features' => '', 'compare_values' => '', 'ribbon' => '', 'featured' => 0, 'online_purchase' => 1, 'active' => 1, 'sort_order' => count($plans) + 1];

$title = 'Precios del home';
$area = 'admin';
$nav = 'precios';
require APP_ROOT . '/app/views/panel_header.php';
?>
<div class="page-head">
  <div><h1>Precios y planes del sitio</h1><p>Todo lo que cambies aquí se publica al instante en la página de inicio y en complementos.</p></div>
  <div class="actions"><a class="btn" href="<?= e(url('index.php#planes')) ?>" target="_blank" rel="noopener">Ver planes en el sitio ↗</a></div>
</div>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="stack">
<?php foreach (array_merge($plans, [$newPlan]) as $pl): $isNew = !$pl['id']; ?>
  <details class="card" id="plan-<?= (int) $pl['id'] ?>"<?= $isNew ? '' : ' open' ?>>
    <summary class="card-head" style="cursor:pointer;list-style:none">
      <h2><?= $isNew ? '+ Agregar un plan nuevo' : e($pl['name']) ?></h2>
      <?php if (!$isNew): ?>
        <div style="display:flex;gap:.5rem;align-items:center">
          <?php if (!$pl['active']): ?><span class="pill pill-muted">Oculto</span><?php endif; ?>
          <?php if ($pl['featured']): ?><span class="pill pill-info">Destacado</span><?php endif; ?>
          <b><?= e(money($pl['price_cop'])) ?></b>
        </div>
      <?php endif; ?>
    </summary>
    <form method="post" class="card-body">
      <?= csrf_field() ?><input type="hidden" name="section" value="plan"><input type="hidden" name="id" value="<?= (int) $pl['id'] ?>">
      <div class="grid g-2">
        <div class="form-grid" style="align-content:start">
          <div class="field"><label>Nombre</label><input name="name" required value="<?= e($pl['name']) ?>"></div>
          <div class="field"><label>Precio anual (COP, sin impuestos)</label><input name="price_cop" required inputmode="decimal" value="<?= e(num_input($pl['price_cop'])) ?>"></div>
          <div class="field"><label>Capacidad</label><input name="capacity_label" value="<?= e($pl['capacity_label']) ?>" placeholder="Hasta 50 equipos"></div>
          <div class="field"><label>Periodo</label><input name="period_label" value="<?= e($pl['period_label']) ?>" placeholder="por año · hasta 50 equipos"></div>
          <div class="field full"><label>Subtítulo</label><input name="subtitle" value="<?= e($pl['subtitle']) ?>"></div>
          <div class="field"><label>Etiqueta destacada</label><input name="ribbon" value="<?= e($pl['ribbon']) ?>" placeholder="Más solicitado"></div>
          <div class="field"><label>Orden</label><input name="sort_order" type="number" value="<?= (int) $pl['sort_order'] ?>"></div>
          <div class="field"><label>Identificador (URL)</label><input name="slug" value="<?= e($pl['slug']) ?>" placeholder="se genera del nombre"></div>
          <div class="field full" style="display:flex;gap:1.2rem;flex-wrap:wrap">
            <label class="check"><input type="checkbox" name="active" value="1"<?= $pl['active'] ? ' checked' : '' ?>> Visible en el sitio</label>
            <label class="check"><input type="checkbox" name="featured" value="1"<?= $pl['featured'] ? ' checked' : '' ?>> Plan destacado</label>
            <label class="check"><input type="checkbox" name="online_purchase" value="1"<?= $pl['online_purchase'] ? ' checked' : '' ?>> Permitir compra en línea</label>
          </div>
        </div>
        <div>
          <div class="field"><label>Características (una por línea)</label><textarea name="features" rows="8"><?= e($pl['features']) ?></textarea></div>
          <div class="field">
            <label>Valores de la tabla comparativa (una línea por fila)</label>
            <textarea name="compare_values" rows="<?= max(4, count($labels)) ?>" placeholder="<?= e(implode("\n", $labels)) ?>"><?= e($pl['compare_values']) ?></textarea>
            <div class="hint">En el mismo orden de las filas: <?= e(implode(' · ', $labels)) ?>. Usa «Sí» o «—».</div>
          </div>
        </div>
      </div>
      <div style="display:flex;gap:.5rem;justify-content:space-between;flex-wrap:wrap">
        <button class="btn btn-primary" type="submit"><?= $isNew ? 'Crear plan' : 'Guardar plan' ?></button>
      </div>
    </form>
    <?php if (!$isNew): ?>
      <form method="post" class="card-body" style="padding-top:0" data-confirm="¿Eliminar el plan «<?= e($pl['name']) ?>»? Si tiene documentos asociados solo se ocultará.">
        <?= csrf_field() ?><input type="hidden" name="section" value="plan_delete"><input type="hidden" name="id" value="<?= (int) $pl['id'] ?>">
        <button class="btn btn-sm btn-danger">Eliminar plan</button>
      </form>
    <?php endif; ?>
  </details>
<?php endforeach; ?>

  <form class="card" method="post" id="complementos">
    <div class="card-head"><h2>Precios de complementos</h2><a class="link small" href="<?= e(url('complementos.php#precios')) ?>" target="_blank" rel="noopener">Ver en el sitio ↗</a></div>
    <?= csrf_field() ?><input type="hidden" name="section" value="addons">
    <div class="table-wrap"><table class="tbl items-table">
      <thead><tr><th>Tipo de organización</th><th class="c-price">Un complemento</th><th class="c-price">Paquete completo</th><th>Incluye</th><th>A medida</th><th>Quitar</th></tr></thead>
      <tbody>
      <?php foreach (array_merge($addons, [['id' => 0, 'org_type' => '', 'single_price' => null, 'bundle_price' => null, 'includes' => '', 'custom_quote' => 0]]) as $i => $a): ?>
        <tr>
          <td class="c-desc"><input type="hidden" name="addon_id[]" value="<?= (int) $a['id'] ?>"><input class="input" name="addon_org[]" value="<?= e($a['org_type']) ?>" placeholder="<?= $a['id'] ? '' : 'Nueva fila…' ?>"></td>
          <td class="c-price"><input class="input" name="addon_single[]" value="<?= $a['single_price'] === null ? '' : e(num_input($a['single_price'])) ?>" inputmode="decimal"></td>
          <td class="c-price"><input class="input" name="addon_bundle[]" value="<?= $a['bundle_price'] === null ? '' : e(num_input($a['bundle_price'])) ?>" inputmode="decimal"></td>
          <td><input class="input" name="addon_includes[]" value="<?= e($a['includes']) ?>"></td>
          <td class="center"><input type="checkbox" name="addon_custom[<?= $i ?>]" value="1"<?= $a['custom_quote'] ? ' checked' : '' ?> aria-label="Propuesta a medida"></td>
          <td class="center"><?php if ($a['id']): ?><input type="checkbox" name="addon_delete[<?= (int) $a['id'] ?>]" value="1" aria-label="Quitar fila"><?php endif; ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <div class="card-body"><p class="small muted mb-2">Deja un precio vacío para mostrar «A consultar». «A medida» muestra el enlace a cotización en lugar de precios.</p><button class="btn btn-primary">Guardar complementos</button></div>
  </form>

  <form class="card" method="post" id="general">
    <div class="card-head"><h2>Ajustes del home</h2></div>
    <div class="card-body">
      <?= csrf_field() ?><input type="hidden" name="section" value="general">
      <div class="form-grid">
        <div class="field"><label>Tasa de conversión (COP por 1 EUR)</label><input name="eur_rate" inputmode="decimal" value="<?= e(setting('eur_rate', '5000')) ?>"><div class="hint">Se usa en el selector de moneda del sitio (solo informativo; los cobros son en COP).</div></div>
        <div class="field"><label>Versión de Veyon mostrada</label><input name="hero_version" value="<?= e(setting('hero_version', '4.11.2')) ?>"></div>
        <div class="field full"><label>Filas de la tabla comparativa (una por línea)</label><textarea name="compare_labels" rows="9"><?= e(setting('compare_labels')) ?></textarea></div>
      </div>
      <button class="btn btn-primary">Guardar ajustes</button>
    </div>
  </form>
</div>
<?php require APP_ROOT . '/app/views/panel_footer.php'; ?>
