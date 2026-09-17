<?php
/**
 * Maquetación compartida del panel de administración y del portal de clientes.
 * @var string $area  'admin' | 'portal'
 * @var string $title
 * @var string $nav   clave del menú activo
 */
$area = $area ?? 'portal';
$nav = $nav ?? '';

$icons = [
    'home'     => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
    'users'    => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
    'invoice'  => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/>',
    'quote'    => '<path d="M9 11l3 3 8-8"/><path d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h9"/>',
    'payment'  => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
    'license'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
    'tag'      => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><circle cx="7" cy="7" r="1.5"/>',
    'inbox'    => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.8 4H7.2a2 2 0 0 0-1.7 1.1z"/>',
    'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
    'shield'   => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
    'user'     => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    'globe'    => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/>',
    'activity' => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
];
$icon = fn (string $name) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($icons[$name] ?? '') . '</svg>';

if ($area === 'admin') {
    $newLeads = (int) db_val("SELECT COUNT(*) FROM leads WHERE status = 'new'");
    $menu = [
        ['Principal', null, null, null],
        ['Resumen', 'admin/index.php', 'home', 'dashboard'],
        ['Clientes', 'admin/clientes.php', 'users', 'clientes'],
        ['Solicitudes', 'admin/solicitudes.php', 'inbox', 'solicitudes', $newLeads],
        ['Facturación', null, null, null],
        ['Cotizaciones', 'admin/cotizaciones.php', 'quote', 'cotizaciones'],
        ['Facturas', 'admin/facturas.php', 'invoice', 'facturas'],
        ['Pagos', 'admin/pagos.php', 'payment', 'pagos'],
        ['Licencias', 'admin/licencias.php', 'license', 'licencias'],
        ['Sitio web', null, null, null],
        ['Precios del home', 'admin/precios.php', 'tag', 'precios'],
        ['Configuración', 'admin/ajustes.php', 'settings', 'ajustes'],
        ['Administradores', 'admin/usuarios.php', 'shield', 'usuarios'],
        ['Actividad', 'admin/actividad.php', 'activity', 'actividad'],
    ];
    $who = current_admin();
    $whoName = $who['name'] ?? '';
    $whoSub = $who['email'] ?? '';
    $areaLabel = 'Administración';
} else {
    $menu = [
        ['Resumen', 'portal/index.php', 'home', 'inicio'],
        ['Facturas', 'portal/facturas.php', 'invoice', 'facturas'],
        ['Cotizaciones', 'portal/cotizaciones.php', 'quote', 'cotizaciones'],
        ['Pagos', 'portal/pagos.php', 'payment', 'pagos'],
        ['Mis datos', 'portal/perfil.php', 'user', 'perfil'],
    ];
    $who = current_client();
    $whoName = $who['institution'] ?? '';
    $whoSub = $who['contact_name'] ?: ($who['email'] ?? '');
    $areaLabel = 'Portal de clientes';
}
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title ?? $areaLabel) ?> · <?= e(setting('company_name', 'Veyon Control')) ?></title>
<link rel="icon" href="<?= e(url('assets/img/logo.svg')) ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&family=JetBrains+Mono:wght@500&display=swap">
<link rel="stylesheet" href="<?= e(asset('assets/css/panel.css')) ?>">
</head>
<body>
<div class="shell">
  <aside class="side" id="side">
    <a class="brand" href="<?= e(url($area === 'admin' ? 'admin/index.php' : 'portal/index.php')) ?>">
      <img src="<?= e(url('assets/img/logo.svg')) ?>" alt="">
      <span><?= e(setting('company_name', 'Veyon Control')) ?><small><?= e($areaLabel) ?></small></span>
    </a>
    <nav class="side-nav" aria-label="<?= e($areaLabel) ?>">
      <?php foreach ($menu as $item): ?>
        <?php if ($item[1] === null): ?>
          <div class="label"><?= e($item[0]) ?></div>
        <?php else: ?>
          <a href="<?= e(url($item[1])) ?>"<?= $nav === $item[3] ? ' class="on" aria-current="page"' : '' ?>>
            <?= $icon($item[2]) ?><span><?= e($item[0]) ?></span>
            <?php if (!empty($item[4])): ?><span class="count"><?= (int) $item[4] ?></span><?php endif; ?>
          </a>
        <?php endif; ?>
      <?php endforeach; ?>
      <a href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener"><?= $icon('globe') ?><span>Ver sitio web</span></a>
    </nav>
    <div class="side-foot">
      <div class="who"><?= e($whoName) ?><small><?= e($whoSub) ?></small></div>
      <div class="actions">
        <?php if ($area === 'admin'): ?><a class="btn btn-sm" href="<?= e(url('admin/perfil.php')) ?>">Mi cuenta</a><?php endif; ?>
        <form method="post" action="<?= e(url($area === 'admin' ? 'admin/logout.php' : 'portal/logout.php')) ?>" class="inline">
          <?= csrf_field() ?><button class="btn btn-sm btn-ghost" type="submit">Cerrar sesión</button>
        </form>
      </div>
    </div>
  </aside>

  <div class="main">
    <header class="topbar">
      <a class="brand" href="<?= e(url($area === 'admin' ? 'admin/index.php' : 'portal/index.php')) ?>"><img src="<?= e(url('assets/img/logo.svg')) ?>" alt=""><?= e($areaLabel) ?></a>
      <button class="menu-btn" type="button" data-toggle-side aria-label="Abrir menú" aria-controls="side">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
      </button>
    </header>
    <div class="content">
      <?php foreach (flashes() as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>" role="status"><?= e($f['message']) ?></div>
      <?php endforeach; ?>
