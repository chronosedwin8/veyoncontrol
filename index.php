<?php
require __DIR__ . '/app/bootstrap.php';

$page = [
    'title'       => 'Veyon Control — Gestión y control de aulas informáticas',
    'description' => 'Veyon Control: licenciamiento, actualizaciones y soporte de Veyon para aulas y laboratorios. Planes desde 10 equipos hasta licencia de sitio, con capacitación y soporte incluidos.',
    'active'      => 'inicio',
    'preload'     => 'assets/img/veyon-master-1.jpg',
];
$plans = plans_catalog(true);
$compareLabels = lines(setting('compare_labels'));
$taxRate = (float) setting('tax_rate', '0');
$minPlan = $plans ? min(array_map(fn ($p) => (float) $p['price_cop'], $plans)) : 0;
$firstPlan = $plans[0] ?? null;
$version = setting('hero_version', '4.11.2');
require APP_ROOT . '/app/views/public_header.php';
?>


<!-- ================= HERO ================= -->
<section class="hero">
  <div class="container">
    <div class="hero-grid">
      <div class="hero-text reveal in">
        <span class="eyebrow">Versión <?= e($version) ?> · Actualizaciones incluidas</span>
        <h1>El aula informática,<br><span class="grad-text" data-typing='["bajo control.","en una pantalla.","sin distracciones.","a tu ritmo."]'>bajo control.</span></h1>
        <p class="lead">Ve, guía y asiste cada equipo del aula desde un único panel: pantallas en vivo, control remoto, demostraciones a pantalla completa, bloqueo de puestos y envío de archivos. Nosotros mantenemos cada puesto al día con las nuevas versiones, capacitamos a tu equipo docente y respondemos cuando algo falla.</p>

        <div class="hero-actions">
          <a href="#planes" class="btn btn-primary btn-lg">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"></path><path d="M4 6v12a2 2 0 0 0 2 2h14v-4"></path><circle cx="18" cy="14" r="1.5"></circle></svg>
            Ver planes y precios
          </a>
          <a href="#video" class="btn btn-ghost btn-lg">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
            Ver el video
          </a>
        </div>

        <div class="hero-meta">
          <div><span class="dot"></span> <b><?= e($version) ?></b> versión implantada</div>
          <div>· <b>Windows</b> y <b>Linux</b></div>
          <div>· desde <b data-cop="<?= e(num_input($minPlan)) ?>"><?= e(money($minPlan)) ?></b> por año</div>
        </div>
      </div>

      <div class="hero-visual reveal in" data-d="2">
        <div class="frame">
          <div class="frame-bar"><i></i><i></i><i></i></div>
          <img src="assets/img/veyon-master-1.jpg" alt="Panel principal de Veyon Master mostrando las miniaturas de los equipos del aula" width="1200" height="750" fetchpriority="high" decoding="async">
        </div>

        <div class="float-card fc-1">
          <div class="ic">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"></rect><path d="M8 21h8M12 17v4"></path></svg>
          </div>
          <div>24 equipos<small>Conectados y visibles</small></div>
        </div>

        <div class="float-card fc-2">
          <div class="ic">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          </div>
          <div>Cifrado TLS<small>Claves propias por centro</small></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= MARQUESINA ================= -->
<div class="marquee">
  <div class="marquee-track">
    <span>Windows 10 · 11</span><span>Windows Server</span><span>Debian</span><span>Ubuntu</span>
    <span>Fedora</span><span>openSUSE</span><span>RHEL · Rocky Linux</span><span>LDAP · Active Directory</span>
    <span>Microsoft Entra ID</span><span>Capacitación incluida</span><span>Soporte en español</span>
    <span>Windows 10 · 11</span><span>Windows Server</span><span>Debian</span><span>Ubuntu</span>
    <span>Fedora</span><span>openSUSE</span><span>RHEL · Rocky Linux</span><span>LDAP · Active Directory</span>
    <span>Microsoft Entra ID</span><span>Capacitación incluida</span><span>Soporte en español</span>
  </div>
</div>

<!-- ================= GARANTÍAS ================= -->
<section style="padding:clamp(2.5rem,5vw,3.5rem) 0 0">
  <div class="container">
    <div class="assurance reveal">
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg><div><b>Actualizaciones incluidas</b><span>Cada puesto siempre en la última versión</span></div></div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg><div><b>Soporte con respuesta</b><span>48 horas hábiles durante la vigencia</span></div></div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.9"></path></svg><div><b>Capacitación docente</b><span>Para que se use desde el primer día</span></div></div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path></svg><div><b>Factura institucional</b><span>Documentación para contratación</span></div></div>
    </div>
  </div>
</section>

<!-- ================= ESTADÍSTICAS ================= -->
<section style="padding-top:clamp(3rem,6vw,5rem)">
  <div class="container">
    <div class="stats reveal">
      <div class="stat"><b data-count="4.11" data-decimals="2">0</b><span>Versión que implantamos</span></div>
      <div class="stat"><b data-count="48" data-suffix=" h">0</b><span>Respuesta de soporte prioritario</span></div>
      <div class="stat"><b data-count="30" data-suffix="+">0</b><span>Idiomas de la interfaz</span></div>
      <div class="stat"><b data-cop="<?= e(num_input($minPlan)) ?>" style="font-size:clamp(1.4rem,2.6vw,2rem)"><?= e(money($minPlan)) ?></b><span><?= $firstPlan ? e($firstPlan['name'] . ', ' . mb_strtolower($firstPlan['capacity_label'])) : 'Licencia anual' ?></span></div>
    </div>
  </div>
</section>

<!-- ================= PILARES ================= -->
<section id="funciones" class="anchor">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Qué hace Veyon</span>
      <h2>Todo lo que necesitas para <span class="grad-text">dirigir una clase digital</span></h2>
      <p class="lead" style="margin-inline:auto">Cuatro bloques de funciones integradas, listas desde la instalación y sin servicios externos de por medio.</p>
    </div>

    <div class="grid g-4">
      <article class="card reveal" data-d="1">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="4" width="20" height="14" rx="2"></rect><path d="M8 22h8M12 18v4"></path></svg></div>
        <h3>Supervisión en vivo</h3>
        <p>Miniaturas en tiempo real de todos los puestos, con actualización configurable y vista ampliada de cualquier equipo.</p>
      </article>

      <article class="card reveal" data-d="2">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 2v6M12 22v-6"></path><circle cx="12" cy="12" r="4"></circle><path d="M4.9 4.9l4.2 4.2M14.9 14.9l4.2 4.2M19.1 4.9l-4.2 4.2M9.1 14.9l-4.2 4.2"></path></svg></div>
        <h3>Control remoto</h3>
        <p>Toma el teclado y el ratón de un equipo para resolver dudas sin levantarte, o accede en modo solo lectura.</p>
      </article>

      <article class="card reveal" data-d="3">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 5h18v12H3z"></path><path d="M8 21h8"></path><path d="M7 11l3 3 6-6"></path></svg></div>
        <h3>Demostración</h3>
        <p>Emite tu pantalla —o la de un alumno— a toda la clase, a pantalla completa o en ventana para que puedan seguir trabajando.</p>
      </article>

      <article class="card reveal" data-d="4">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16v16H4z"></path><path d="M9 9h6v6H9z"></path></svg></div>
        <h3>Bloqueo y atención</h3>
        <p>Congela pantallas, teclados y ratones con un clic para recuperar la atención del grupo en el momento clave.</p>
      </article>
    </div>
  </div>
</section>

<!-- ================= BLOQUES DETALLADOS ================= -->
<section style="padding-top:0">
  <div class="container">

    <!-- Monitorizar -->
    <div class="feature-row">
      <div class="fr-text reveal">
        <span class="eyebrow">Monitorizar y controlar</span>
        <h2>Toda el aula en <span class="grad-text">una sola pantalla</span></h2>
        <p>Veyon Master muestra cada ordenador como una miniatura viva. Ordena los puestos según la distribución real del aula, agrúpalos por ubicaciones y actúa sobre uno, varios o todos a la vez.</p>
        <ul class="fr-list">
          <li><span class="chk">✓</span><span>Vista en directo con tamaño de miniatura e intervalo ajustables.</span></li>
          <li><span class="chk">✓</span><span>Control remoto completo o acceso en modo observación.</span></li>
          <li><span class="chk">✓</span><span>Bloqueo instantáneo de pantalla, teclado y ratón.</span></li>
          <li><span class="chk">✓</span><span>Encendido por red, cierre de sesión, reinicio y apagado del grupo.</span></li>
        </ul>
        <a href="descargas.php" class="btn btn-ghost">Probar en tu aula</a>
      </div>
      <div class="fr-media reveal" data-d="2">
        <div class="glow"></div>
        <div class="frame"><img src="assets/img/veyon-master-2.png" alt="Veyon Master con la vista de equipos del aula y la barra de funciones" loading="lazy"></div>
      </div>
    </div>

    <!-- Demo -->
    <div class="feature-row rev">
      <div class="fr-text reveal">
        <span class="eyebrow">Demostración</span>
        <h2>Enseña una vez, <span class="grad-text">llega a todos</span></h2>
        <p>Comparte tu escritorio con el grupo entero para explicar un procedimiento paso a paso. En modo ventana, el alumnado puede seguir la explicación mientras reproduce los mismos pasos en su equipo.</p>
        <ul class="fr-list">
          <li><span class="chk">✓</span><span>Emisión a pantalla completa para captar toda la atención.</span></li>
          <li><span class="chk">✓</span><span>Modo ventana para practicar mientras se observa.</span></li>
          <li><span class="chk">✓</span><span>Cualquier equipo puede convertirse en la fuente de la demostración.</span></li>
          <li><span class="chk">✓</span><span>Transmisión optimizada para redes de centro educativo.</span></li>
        </ul>
      </div>
      <div class="fr-media reveal" data-d="2">
        <div class="glow"></div>
        <div class="frame" style="padding:1.6rem;background:linear-gradient(150deg,rgba(34,211,238,.08),rgba(139,92,246,.06))">
          <img src="assets/img/demo.png" alt="Ilustración de la función de demostración de Veyon" loading="lazy">
        </div>
      </div>
    </div>

    <!-- Archivos -->
    <div class="feature-row">
      <div class="fr-text reveal">
        <span class="eyebrow">Transferencia de archivos</span>
        <h2>Reparte y recoge <span class="grad-text">material sin USB</span></h2>
        <p>Envía enunciados, plantillas o recursos a todos los puestos de una vez, y decide si el archivo simplemente se guarda o se abre automáticamente al llegar. Ideal para exámenes y prácticas guiadas.</p>
        <ul class="fr-list">
          <li><span class="chk">✓</span><span>Envío simultáneo a toda la clase o a una selección.</span></li>
          <li><span class="chk">✓</span><span>Apertura automática del documento en el equipo de destino.</span></li>
          <li><span class="chk">✓</span><span>Destino configurable dentro del perfil de cada usuario.</span></li>
          <li><span class="chk">✓</span><span>Sin memorias USB ni servicios externos en la ecuación.</span></li>
        </ul>
      </div>
      <div class="fr-media reveal" data-d="2">
        <div class="glow"></div>
        <div class="frame" style="padding:1.6rem;background:linear-gradient(150deg,rgba(52,224,161,.08),rgba(34,211,238,.06))">
          <img src="assets/img/filetransfer.png" alt="Ilustración de la transferencia de archivos entre docente y alumnado" loading="lazy">
        </div>
      </div>
    </div>

    <!-- LDAP -->
    <div class="feature-row rev">
      <div class="fr-text reveal">
        <span class="eyebrow">Integración</span>
        <h2>Se conecta con la <span class="grad-text">infraestructura que ya tienes</span></h2>
        <p>Veyon puede leer aulas, equipos y grupos directamente de tu directorio, de modo que el panel se mantiene actualizado sin listas manuales. Compatible con servidores LDAP, Active Directory y —mediante complemento— Microsoft Entra ID.</p>
        <ul class="fr-list">
          <li><span class="chk">✓</span><span>Importación de ubicaciones y equipos desde LDAP o AD.</span></li>
          <li><span class="chk">✓</span><span>Autenticación por clave criptográfica o por credenciales de usuario.</span></li>
          <li><span class="chk">✓</span><span>Despliegue silencioso y configuración centralizada por línea de comandos.</span></li>
          <li><span class="chk">✓</span><span>Descubrimiento automático de equipos con el complemento de red.</span></li>
        </ul>
        <a href="complementos.php" class="btn btn-ghost">Ver complementos</a>
      </div>
      <div class="fr-media reveal" data-d="2">
        <div class="glow"></div>
        <div class="frame"><img src="assets/img/ldap.png" alt="Esquema de integración de Veyon con LDAP y Active Directory" loading="lazy"></div>
      </div>
    </div>

  </div>
</section>


<!-- ================= PLANES Y PRECIOS ================= -->
<section class="anchor" id="planes">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Planes y licencias</span>
      <h2>Licenciamiento <span class="grad-text">según el tamaño de tu aula</span></h2>
      <p class="lead" style="margin-inline:auto">Cada plan cubre la licencia anual, las actualizaciones de versión en todos los puestos, la capacitación del profesorado y doce meses de soporte. Precios en <span data-cur-code>COP</span>, <?= $taxRate > 0 ? '+ ' . e(setting('tax_label', 'IVA')) . ' ' . e(num_input($taxRate)) . ' %' : 'sin IVA' ?>, con vigencia de un año.</p>
    </div>

    <div class="plans">
<?php foreach ($plans as $i => $plan): ?>
      <article class="plan<?= $plan['featured'] ? ' featured' : '' ?> reveal anchor" id="plan-<?= e($plan['slug']) ?>" data-d="<?= min(4, $i + 1) ?>">
        <?php if ($plan['ribbon']): ?><span class="ribbon"><?= e($plan['ribbon']) ?></span><?php endif; ?>
        <span class="cap"><?= e($plan['capacity_label']) ?></span>
        <h3><?= e($plan['name']) ?></h3>
        <p class="sub"><?= e($plan['subtitle']) ?></p>
        <div class="price"><span class="amount" data-cop="<?= e(num_input($plan['price_cop'])) ?>"><?= e(money($plan['price_cop'])) ?></span><span class="cur" data-cur-code>COP</span></div>
        <p class="per"><?= e($plan['period_label']) ?></p>
        <ul>
          <?php foreach (lines($plan['features']) as $feature): ?><li><?= e($feature) ?></li><?php endforeach; ?>
        </ul>
        <div class="plan-actions">
          <?php if ($plan['online_purchase'] && (float) $plan['price_cop'] > 0): ?>
            <a href="<?= e(url('checkout.php?plan=' . rawurlencode($plan['slug']))) ?>" class="btn <?= $plan['featured'] ? 'btn-primary' : 'btn-dark' ?>">Comprar en línea</a>
          <?php else: ?>
            <?php /* Sin compra en línea: la tarjeta no queda sin acción */ ?>
            <a href="#contacto" class="btn btn-dark" data-plan-request="<?= e($plan['capacity_label']) ?>">Solicitar cotización</a>
          <?php endif; ?>
        </div>
      </article>
<?php endforeach; ?>
    </div>

    <div class="price-note reveal">
      <span><b>Vigencia:</b> <?= e(setting('license_months', '12')) ?> meses desde la activación</span>
      <span><b>Pago seguro:</b> Mercado Pago (tarjeta, PSE, efectivo)</span>
      <span><b>Multisede:</b> presupuesto a medida</span>
      <span><b>Moneda:</b> cámbiala en la barra superior</span>
    </div>

<?php if ($plans && $compareLabels): ?>
    <div class="section-head reveal mt-5" style="margin-bottom:1.6rem">
      <h3 style="font-size:clamp(1.3rem,2.4vw,1.8rem)">Comparativa rápida de planes</h3>
    </div>

    <div class="table-wrap reveal">
      <table class="cmp">
        <thead>
          <tr><th scope="col">Qué incluye</th><?php foreach ($plans as $plan): ?><th scope="col"><?= e(preg_replace('/^Licencia (de )?/u', '', $plan['name'])) ?></th><?php endforeach; ?></tr>
        </thead>
        <tbody>
<?php foreach ($compareLabels as $row => $label): ?>
          <tr><th scope="row"><?= e($label) ?></th><?php foreach ($plans as $plan):
              $val = lines($plan['compare_values'])[$row] ?? '—';
              $cls = in_array(mb_strtolower($val), ['sí', 'si', 'incluido'], true) ? 'yes' : (in_array($val, ['—', '-', 'No', ''], true) ? 'no' : '');
            ?><td<?= $cls ? ' class="' . $cls . '"' : '' ?>><?= e($val === '-' ? '—' : $val) ?></td><?php endforeach; ?></tr>
<?php endforeach; ?>
          <tr><th scope="row">Precio anual</th><?php foreach ($plans as $plan): ?><td><b data-cop="<?= e(num_input($plan['price_cop'])) ?>"><?= e(money($plan['price_cop'])) ?></b></td><?php endforeach; ?></tr>
        </tbody>
      </table>
    </div>
<?php endif; ?>
  </div>
</section>

<!-- ================= CAPTURAS ================= -->
<section class="section-alt anchor" id="capturas">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Interfaz</span>
      <h2>Tres herramientas, <span class="grad-text">un mismo sistema</span></h2>
      <p class="lead" style="margin-inline:auto">Veyon se instala como un conjunto: el panel del docente, el configurador del administrador y una herramienta de línea de comandos para automatizarlo todo.</p>
    </div>

    <div data-tabs class="reveal">
      <div class="tabs" role="tablist">
        <button class="tab active" role="tab" aria-selected="true" data-target="shot-master">Veyon Master</button>
        <button class="tab" role="tab" aria-selected="false" data-target="shot-config">Configurador</button>
        <button class="tab" role="tab" aria-selected="false" data-target="shot-cli">Línea de comandos</button>
        <button class="tab" role="tab" aria-selected="false" data-target="shot-features">Funciones</button>
      </div>

      <div class="tab-panel active" id="shot-master">
        <div class="frame"><img src="assets/img/veyon-master-1.jpg" alt="Vista general de Veyon Master con las miniaturas de los equipos" loading="lazy"></div>
        <p class="shot-caption">Veyon Master: el puesto de mando del profesorado, con las miniaturas de todos los equipos.</p>
      </div>

      <div class="tab-panel" id="shot-config">
        <div class="frame"><img src="assets/img/veyon-configurator-1.png" alt="Veyon Configurator, la herramienta de configuración del administrador" loading="lazy"></div>
        <p class="shot-caption">Veyon Configurator: claves, autenticación, servicio, red y directorio, todo en un mismo lugar.</p>
      </div>

      <div class="tab-panel" id="shot-cli">
        <div class="frame"><img src="assets/img/veyon-cli-1.png" alt="Herramienta de línea de comandos de Veyon" loading="lazy"></div>
        <p class="shot-caption">Veyon CTL: automatiza el despliegue, la importación de aulas y la gestión de claves por script.</p>
      </div>

      <div class="tab-panel" id="shot-features">
        <div class="frame"><img src="assets/img/veyon-features.png" alt="Panel de funciones disponibles en Veyon" loading="lazy"></div>
        <p class="shot-caption">Cada función del aula, accesible desde la barra lateral del panel principal.</p>
      </div>
    </div>
  </div>
</section>


<!-- ================= VIDEO ================= -->
<section class="anchor" id="video">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Guía en video</span>
      <h2>Instalación y uso, <span class="grad-text">paso a paso</span></h2>
      <p class="lead" style="margin-inline:auto">Un recorrido en video por la instalación y el uso diario de Veyon. Útil para el área de TI y como material de repaso para el profesorado.</p>
    </div>

    <div class="hero-grid" style="align-items:start">
      <div class="reveal" style="grid-column:span 1">
        <div class="video-wrap">
          <div class="video-ratio">
            <button type="button" class="video-poster" data-yt="MP0ypeqDndM" data-title="Instalación y uso de Veyon"
              style="background-image:url('https://i.ytimg.com/vi/MP0ypeqDndM/hqdefault.jpg')" aria-label="Reproducir el video: Instalación y uso de Veyon">
              <span><svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="7 4 20 12 7 20 7 4"></polygon></svg></span>
            </button>
          </div>
        </div>
        <p class="small muted mt-2">¿Prefieres verlo en YouTube? <a href="https://youtu.be/MP0ypeqDndM" target="_blank" rel="noopener" class="link">Abrir el video en una pestaña nueva ↗</a></p>
      </div>

      <div class="reveal" data-d="2">
        <h3 class="mb-2">Qué vas a encontrar</h3>
        <ul class="video-chapters">
          <li><span class="t">01</span><span>Instalación del panel en el equipo del docente</span></li>
          <li><span class="t">02</span><span>Instalación del servicio en los puestos del aula</span></li>
          <li><span class="t">03</span><span>Claves de acceso y autenticación entre equipos</span></li>
          <li><span class="t">04</span><span>Alta del aula y de los equipos en el panel</span></li>
          <li><span class="t">05</span><span>Uso diario: supervisar, controlar, bloquear y demostrar</span></li>
        </ul>
        <div class="flex mt-3">
          <a href="descargas.php" class="btn btn-ghost btn-sm">Ir a descargas</a>
          <a href="#planes" class="btn btn-primary btn-sm">Ver planes</a>
        </div>
        <p class="small muted mt-3">Con cualquiera de los planes mantenemos cada puesto en la última versión y capacitamos al equipo docente durante todo el año.</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= CÓMO EMPEZAR ================= -->
<section>
  <div class="container">
    <div class="hero-grid" style="align-items:start">
      <div>
        <div class="section-head left reveal" style="margin-bottom:2rem">
          <span class="eyebrow">Cómo trabajamos</span>
          <h2>De la primera llamada <span class="grad-text">al aula funcionando</span></h2>
        </div>
        <div class="steps">
          <div class="step reveal" data-d="1">
            <div class="num"></div>
            <div><h3>Levantamiento</h3><p>Revisamos cuántos equipos hay, qué sistemas operativos usan y cómo está la red. De ahí sale el plan que te corresponde y el cronograma.</p></div>
          </div>
          <div class="step reveal" data-d="2">
            <div class="num"></div>
            <div><h3>Licencia y actualización</h3><p>Activamos la licencia del plan y dejamos todos los puestos en la versión vigente, con los paquetes listos para las próximas actualizaciones.</p></div>
          </div>
          <div class="step reveal" data-d="3">
            <div class="num"></div>
            <div><h3>Capacitación</h3><p>Formamos al profesorado en el uso diario y al área de TI en la administración, con material de apoyo que queda en la institución.</p></div>
          </div>
          <div class="step reveal" data-d="4">
            <div class="num"></div>
            <div><h3>Soporte y actualizaciones</h3><p>Durante la vigencia atendemos incidencias, publicamos cada nueva versión en tus equipos y revisamos periódicamente que todo esté al día.</p></div>
          </div>
        </div>
      </div>

      <div class="reveal" data-d="2" style="position:sticky;top:110px">
        <div class="terminal">
          <header><i></i><i></i><i></i><small>veyon-ctl · powershell</small>
            <button class="copy-btn" data-copy="#cmd-1">Copiar</button>
          </header>
          <pre id="cmd-1"><span class="c-m"># Instalación silenciosa en el puesto del alumnado</span>
<span class="c-p">$</span> veyon-4.11.2.0-win64-setup.exe /S /NoMaster

<span class="c-m"># Importar la clave pública del centro</span>
<span class="c-p">$</span> veyon-ctl authkeys <span class="c-c">import</span> profesorado/public \
      --filename C:\claves\profesorado_public.pem

<span class="c-m"># Cargar las aulas desde un archivo CSV</span>
<span class="c-p">$</span> veyon-ctl networkobjects <span class="c-c">import</span> aulas.csv \
      --format "%location%;%name%;%host%"

<span class="c-m"># Comprobar el estado del servicio</span>
<span class="c-p">$</span> veyon-ctl service <span class="c-c">status</span>
<span class="c-c">Veyon Service is running.</span></pre>
        </div>
        <p class="small muted mt-2">Tu área de TI recibe los paquetes de actualización y la documentación del proceso, sin quedar atada a nosotros para operar el sistema.</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= CASOS DE USO ================= -->
<section class="section-alt">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Escenarios</span>
      <h2>Pensado para <span class="grad-text">quien enseña cada día</span></h2>
    </div>

    <div class="grid g-3">
      <article class="card reveal" data-d="1">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 10L12 5 2 10l10 5 10-5z"></path><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"></path></svg></div>
        <h3>Aulas de informática</h3>
        <p>Colegios, institutos y centros de formación profesional que necesitan guiar prácticas y mantener el grupo enfocado.</p>
      </article>

      <article class="card reveal" data-d="2">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 3h18v12H3z"></path><path d="M7 21h10M12 15v6"></path></svg></div>
        <h3>Exámenes y evaluaciones</h3>
        <p>Reparto simultáneo del enunciado, bloqueo de equipos al terminar y —con complementos— restricción de internet y de puertos USB.</p>
      </article>

      <article class="card reveal" data-d="3">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3c2.5 3 2.5 15 0 18M12 3c-2.5 3-2.5 15 0 18"></path></svg></div>
        <h3>Formación remota</h3>
        <p>Sesiones de formación distribuidas entre sedes, con acceso a los puestos a través de la red corporativa.</p>
      </article>

      <article class="card reveal" data-d="1">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path></svg></div>
        <h3>Universidades</h3>
        <p>Laboratorios con decenas de puestos, integrados con el directorio institucional y desplegados por imagen del sistema.</p>
      </article>

      <article class="card reveal" data-d="2">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></div>
        <h3>Soporte técnico</h3>
        <p>Asistencia remota puntual a los equipos del centro sin instalar herramientas adicionales de terceros.</p>
      </article>

      <article class="card reveal" data-d="3">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 20h16M4 20V8l8-5 8 5v12"></path><path d="M10 20v-6h4v6"></path></svg></div>
        <h3>Administraciones públicas</h3>
        <p>Centros de formación municipales o autonómicos que requieren software auditable y sin dependencia de licencias.</p>
      </article>
    </div>
  </div>
</section>

<!-- ================= COMPLEMENTOS ================= -->
<section>
  <div class="container">
    <div class="feature-row" style="margin-bottom:0">
      <div class="fr-text reveal">
        <span class="eyebrow">Complementos</span>
        <h2>Amplía Veyon <span class="grad-text">cuando lo necesites</span></h2>
        <p>Sobre cualquiera de los planes puedes sumar complementos que añaden control de acceso a internet, grabación de pantalla, chat, gestión de audio y USB, descubrimiento de red e integración con Microsoft Entra ID. Se licencian aparte y también los mantenemos actualizados junto con el resto del sistema.</p>
        <div class="flex mb-3">
          <span class="badge cyan">Control de internet</span>
          <span class="badge violet">Grabador de pantalla</span>
          <span class="badge">Chat</span>
          <span class="badge amber">Auvidus</span>
        </div>
        <a href="complementos.php" class="btn btn-primary">Ver todos los complementos</a>
      </div>
      <div class="fr-media reveal" data-d="2">
        <div class="glow"></div>
        <div class="frame" style="padding:1.8rem;background:linear-gradient(150deg,rgba(139,92,246,.09),rgba(192,38,211,.06))">
          <img src="assets/img/add-ons.png" alt="Ilustración de los complementos de Veyon" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= FAQ ================= -->
<section class="section-alt anchor" id="faq">
  <div class="container" style="max-width:880px">
    <div class="section-head reveal">
      <span class="eyebrow">Preguntas frecuentes</span>
      <h2>Dudas <span class="grad-text">habituales</span></h2>
    </div>

    <details class="acc reveal" open>
      <summary>¿Qué incluye exactamente el precio de la licencia?</summary>
      <div class="acc-body">
        Cada plan es un servicio completo de licenciamiento y acompañamiento durante doce meses:
        <ul>
          <li>Licencia anual para el número de equipos que cubre el plan.</li>
          <li>Actualizaciones de versión aplicadas en todos los puestos cubiertos.</li>
          <li>Revisión periódica de que cada equipo esté al día y operativo.</li>
          <li>Capacitación al equipo docente y material de apoyo.</li>
          <li>Soporte durante toda la vigencia de la licencia.</li>
        </ul>
      </div>
    </details>

    <details class="acc reveal">
      <summary>¿Qué pasa cuando termina el año de vigencia?</summary>
      <div class="acc-body">Todas las licencias son anuales y se renuevan cada año para seguir en uso. Al acercarse el vencimiento te avisamos con antelación y emitimos la renovación, que mantiene activa la licencia junto con el soporte, las actualizaciones de versión, las revisiones técnicas y las capacitaciones. Si no se renueva, la licencia caduca en la fecha de vencimiento y el acompañamiento se interrumpe. En el momento de renovar puedes continuar con el mismo plan o subir al siguiente si el número de equipos creció.</div>
    </details>

    <details class="acc reveal">
      <summary>¿Y si tengo más equipos de los que cubre mi plan?</summary>
      <div class="acc-body">Se ajusta al plan siguiente y solo se cobra la diferencia proporcional al tiempo restante de vigencia. Si tienes varias sedes o superas ampliamente los 100 equipos, la licencia de sitio suele salir más rentable que sumar planes.</div>
    </details>

    <details class="acc reveal">
      <summary>¿Qué sistemas operativos admite?</summary>
      <div class="acc-body">Windows (32 y 64 bits) y las principales distribuciones Linux: Debian, Ubuntu, Fedora, openSUSE, RHEL y Rocky Linux. Los equipos Windows y Linux pueden convivir en la misma aula y verse en el mismo panel.</div>
    </details>

    <details class="acc reveal">
      <summary>¿Necesito conexión a internet o una cuenta en la nube?</summary>
      <div class="acc-body">No. Todo funciona dentro de la red local de la institución: el panel del docente se comunica directamente con cada equipo. No hay servidor externo obligatorio ni registro de cuentas.</div>
    </details>

    <details class="acc reveal">
      <summary>¿Cómo se protege el acceso a los equipos?</summary>
      <div class="acc-body">
        La comunicación va cifrada con TLS y el acceso se autoriza mediante uno de estos métodos, que tu institución elige según sus políticas:
        <ul>
          <li>Autenticación por clave: la institución tiene su propia pareja de claves y solo quien posee la privada puede conectarse.</li>
          <li>Autenticación por credenciales: se valida el usuario contra el sistema o el directorio, restringido a un grupo concreto.</li>
        </ul>
      </div>
    </details>

    <details class="acc reveal">
      <summary>¿Cómo se realiza el pago y qué comprobante recibo?</summary>
      <div class="acc-body">Puedes pagar en línea con <b>Mercado Pago</b> (tarjeta de crédito o débito, PSE y medios en efectivo) desde el botón «Comprar en línea» o desde el portal de clientes, donde también quedan tus facturas, cotizaciones, pagos y licencias. La contratación se realiza bajo la modalidad de comercio electrónico. Por la diversidad de regímenes tributarios, no nos es posible emitir facturas conforme a la reglamentación fiscal de cada país; todas las operaciones se rigen por las leyes de los Estados Unidos y, en particular, por las del estado de Delaware. Al completar el pago recibes un comprobante electrónico de la transacción, a nombre de la institución, con el detalle del plan contratado, el número de equipos cubiertos y la vigencia anual. Ese documento es el soporte de la compra. Si tu institución requiere condiciones distintas, escríbenos antes de contratar y revisamos tu caso.</div>
    </details>
  </div>
</section>

<!-- ================= LO QUE GANAS ================= -->
<section>
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Lo que ganas</span>
      <h2>Tu aula funcionando <span class="grad-text">desde el primer día</span></h2>
      <p class="lead" style="margin-inline:auto">Llegamos, lo dejamos andando y capacitamos al equipo. Tú solo entras a clase y lo usas.</p>
    </div>

    <div class="value-row">
      <article class="value reveal" data-d="1">
        <span class="n">01 · Siempre al día</span>
        <h3>Nunca una versión atrasada</h3>
        <p>Cada vez que sale una nueva versión, la llevamos a todos tus puestos. Tu equipo de TI no persigue actualizaciones ni revisa aula por aula: el sistema se mantiene al día solo.</p>
      </article>
      <article class="value reveal" data-d="2">
        <span class="n">02 · Tranquilidad</span>
        <h3>Todo bajo control</h3>
        <p>Claves, permisos y red configurados con las mejores prácticas desde el inicio, y un canal de soporte que responde cuando lo necesitas. Sin sorpresas a mitad de clase.</p>
      </article>
      <article class="value reveal" data-d="3">
        <span class="n">03 · Adopción</span>
        <h3>Profesorado que lo usa</h3>
        <p>Capacitamos con casos reales de clase y dejamos material de consulta, para que la herramienta se use de verdad y la inversión se note desde la primera semana.</p>
      </article>
    </div>

    <div class="price-note reveal">
      <span><b>Demostración sin costo</b> sobre tus propios equipos</span>
      <span><b>Precio cerrado</b> antes de empezar</span>
      <span><b>Cronograma definido</b> desde el primer día</span>
    </div>

    <div class="center mt-4 reveal">
      <a href="#planes" class="btn btn-primary btn-lg">Ver planes y precios</a>
    </div>
  </div>
</section>

<!-- ================= CONTACTO ================= -->
<section class="anchor" id="contacto">
  <div class="container">
    <div class="hero-grid" style="align-items:start">

      <div class="reveal">
        <span class="eyebrow">Solicitar propuesta</span>
        <h2>Cuéntanos cómo es <span class="grad-text">tu aula</span></h2>
        <p class="lead mt-2 mb-3">Con el número de equipos y el sistema operativo que usan basta para enviarte una propuesta concreta, con precio cerrado y alcance definido.</p>

        <ul class="fr-list mb-3">
          <li><span class="chk">✓</span><span>Respuesta a la solicitud en menos de <b>48 horas hábiles</b>.</span></li>
          <li><span class="chk">✓</span><span>Demostración en vivo sobre tus propios equipos, sin costo.</span></li>
          <li><span class="chk">✓</span><span>Propuesta con alcance, cronograma y precio cerrado.</span></li>
          <li><span class="chk">✓</span><span>Facturación a nombre de la institución y pago en línea con Mercado Pago.</span></li>
        </ul>

        <div class="panel">
          <h3><span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8.1 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.8 2z"></path></svg></span>Atención comercial</h3>
          <p>Si prefieres hablarlo antes, escríbenos y coordinamos una llamada corta para revisar tu caso.</p>
        </div>
      </div>

      <div class="reveal" data-d="2">
        <form class="panel" id="form-cotizacion" action="<?= e(url('api/lead.php')) ?>" method="post" novalidate>
          <h3 style="margin-bottom:1.4rem">Solicitud de cotización</h3>
          <?= csrf_field() ?>
          <div class="hp" aria-hidden="true"><label>Sitio web <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

          <div class="field">
            <label for="f-inst">Institución <span>*</span></label>
            <input id="f-inst" name="institucion" type="text" required maxlength="190" autocomplete="organization" placeholder="Nombre del colegio, instituto o universidad">
          </div>

          <div class="field-row">
            <div class="field">
              <label for="f-nombre">Nombre de contacto <span>*</span></label>
              <input id="f-nombre" name="nombre" type="text" required maxlength="150" autocomplete="name" placeholder="Nombre y apellido">
            </div>
            <div class="field">
              <label for="f-cargo">Cargo</label>
              <input id="f-cargo" name="cargo" type="text" maxlength="120" autocomplete="organization-title" placeholder="Coordinación, TI, rectoría…">
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="f-mail">Correo <span>*</span></label>
              <input id="f-mail" name="correo" type="email" required maxlength="190" autocomplete="email" placeholder="nombre@institucion.edu.co">
            </div>
            <div class="field">
              <label for="f-tel">Teléfono</label>
              <input id="f-tel" name="telefono" type="tel" maxlength="40" autocomplete="tel" placeholder="+57 300 000 0000">
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="f-equipos">Equipos a cubrir <span>*</span></label>
              <select id="f-equipos" name="equipos" required>
                <option value="">Selecciona…</option>
                <?php foreach ($plans as $plan): ?><option><?= e($plan['capacity_label']) ?></option><?php endforeach; ?>
                <option>Varias sedes / a medida</option>
              </select>
            </div>
            <div class="field">
              <label for="f-so">Sistema operativo</label>
              <select id="f-so" name="so">
                <option value="">Selecciona…</option>
                <option>Windows</option>
                <option>Linux</option>
                <option>Windows y Linux</option>
              </select>
            </div>
          </div>

          <div class="field">
            <label for="f-msg">Cuéntanos brevemente tu caso</label>
            <textarea id="f-msg" name="mensaje" rows="4" maxlength="5000" placeholder="Número de aulas, sedes, fechas previstas, integraciones necesarias…"></textarea>
          </div>

          <button type="submit" class="btn btn-primary" style="width:100%">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            Enviar solicitud
          </button>

          <p class="form-note" id="form-msg" role="status" aria-live="polite">Te respondemos en menos de 48 horas hábiles. Usamos tus datos solo para preparar la propuesta.</p>
        </form>
      </div>
    </div>

    <div class="cta mt-5 reveal">
      <span class="eyebrow">Empieza hoy</span>
      <h2>Tu próxima clase, <span class="grad-text">bajo control</span></h2>
      <p>Elige el plan que corresponde al tamaño de tu aula y nos encargamos del resto: actualizaciones permanentes, capacitación y soporte.</p>
      <div class="hero-actions">
        <a href="#planes" class="btn btn-primary btn-lg">Ver planes y precios</a>
        <a href="#contacto" class="btn btn-ghost btn-lg">Solicitar una demostración</a>
      </div>
    </div>
  </div>
</section>

<?php require APP_ROOT . '/app/views/public_footer.php'; ?>
