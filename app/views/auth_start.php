<?php
/**
 * @var string $title
 * @var string $area 'admin' | 'portal'
 */
$area = $area ?? 'portal';
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> · <?= e(setting('company_name', 'Veyon Control')) ?></title>
<link rel="icon" href="<?= e(url('assets/img/logo.svg')) ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap">
<link rel="stylesheet" href="<?= e(asset('assets/css/panel.css')) ?>">
</head>
<body>
<div class="auth">
  <aside class="auth-art">
    <a class="brand" href="<?= e(url('index.php')) ?>"><img src="<?= e(url('assets/img/logo.svg')) ?>" alt=""><?= e(setting('company_name', 'Veyon Control')) ?></a>
    <div>
      <?php if ($area === 'admin'): ?>
        <h2>Panel de administración</h2>
        <p>Gestiona clientes, precios del sitio, cotizaciones, facturas y pagos desde un único lugar.</p>
      <?php else: ?>
        <h2>Tu aula bajo control, también en la facturación</h2>
        <p>Consulta el estado de tus licencias y paga en línea de forma segura.</p>
        <ul>
          <li>Facturas y cotizaciones siempre disponibles</li>
          <li>Pago con Mercado Pago: tarjeta, PSE o efectivo</li>
          <li>Historial de pagos y vigencia de licencias</li>
        </ul>
      <?php endif; ?>
    </div>
    <small style="opacity:.75">© <?= date('Y') ?> <?= e(setting('company_name', 'Veyon Control')) ?></small>
  </aside>
  <main class="auth-form">
    <div class="auth-box">
      <?php foreach (flashes() as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>" role="status"><?= e($f['message']) ?></div>
      <?php endforeach; ?>
