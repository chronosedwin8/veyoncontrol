<?php
/** @var array $page title, description, active */
$active = $page['active'] ?? '';
$navClient = current_client();
$isActive = fn (string $key) => $active === $key ? ' class="active" aria-current="page"' : '';
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<script>document.documentElement.classList.add("js")</script>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page['title']) ?></title>
<meta name="description" content="<?= e($page['description']) ?>">
<meta name="theme-color" content="#ffffff">
<link rel="canonical" href="<?= e(abs_url(basename($_SERVER['SCRIPT_NAME']) === 'index.php' ? '' : basename($_SERVER['SCRIPT_NAME']))) ?>">
<meta property="og:title" content="<?= e($page['title']) ?>">
<meta property="og:description" content="<?= e($page['description']) ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= e(abs_url('assets/img/veyon-master-1.jpg')) ?>">
<link rel="icon" href="<?= e(url('assets/img/logo.svg')) ?>" type="image/svg+xml">
<link rel="alternate icon" href="<?= e(url('assets/img/favicon.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&family=JetBrains+Mono:wght@500&display=swap">
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<?php if (!empty($page['preload'])): ?><link rel="preload" as="image" href="<?= e(url($page['preload'])) ?>"><?php endif; ?>
</head>
<body data-eur-rate="<?= e(setting('eur_rate', '5000')) ?>">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<div class="scroll-progress"></div>

<header class="nav">
  <div class="container">
    <a class="brand" href="<?= e(url('index.php')) ?>">
      <img src="<?= e(url('assets/img/logo.svg')) ?>" alt="" width="34" height="34">
      <span>Veyon<b>Control</b></span>
    </a>

    <nav class="nav-links" id="menu" aria-label="Principal">
      <a href="<?= e(url('index.php')) ?>"<?= $isActive('inicio') ?>>Inicio</a>
      <a href="<?= e(url('index.php#funciones')) ?>">Funciones</a>
      <a href="<?= e(url('index.php#planes')) ?>">Precios</a>
      <a href="<?= e(url('descargas.php')) ?>"<?= $isActive('descargas') ?>>Descargas</a>
      <a href="<?= e(url('complementos.php')) ?>"<?= $isActive('complementos') ?>>Complementos</a>
      <div class="has-sub">
        <button type="button" class="sub-toggle" aria-expanded="false">Recursos
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>
        <div class="sub-menu">
          <a href="<?= e(url('index.php#video')) ?>">Video tutorial</a>
          <a href="https://docs.veyon.io/es/latest/" class="ext" target="_blank" rel="noopener">Documentación</a>
          <a href="https://veyon.nodebb.com" class="ext" target="_blank" rel="noopener">Foro</a>
          <a href="https://veyon.io/blog/" class="ext" target="_blank" rel="noopener">Noticias</a>
          <a href="<?= e(url('participa.php')) ?>">Soporte</a>
        </div>
      </div>
      <a href="<?= e(url('acerca.php')) ?>"<?= $isActive('acerca') ?>>Nosotros</a>
      <a href="<?= e(url($navClient ? 'portal/index.php' : 'portal/login.php')) ?>" class="nav-account-mobile"><?= $navClient ? 'Mi cuenta' : 'Portal de clientes' ?></a>
    </nav>

    <div class="nav-cta">
      <div class="cur-switch" role="group" aria-label="Selector de moneda">
        <button type="button" data-cur="COP">COP</button>
        <button type="button" data-cur="EUR">EUR</button>
      </div>
      <a href="<?= e(url($navClient ? 'portal/index.php' : 'portal/login.php')) ?>" class="btn btn-ghost btn-sm nav-account">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        <?= $navClient ? 'Mi cuenta' : 'Ingresar' ?>
      </a>
      <a href="<?= e(url('index.php#planes')) ?>" class="btn btn-primary btn-sm">Ver planes</a>
      <button class="burger" aria-label="Abrir menú" aria-expanded="false" aria-controls="menu"><span></span></button>
    </div>
  </div>
</header>

<main id="contenido">
