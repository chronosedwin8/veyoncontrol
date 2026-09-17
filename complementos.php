<?php
require __DIR__ . '/app/bootstrap.php';

$page = [
    'title'       => 'Complementos — Veyon Control',
    'description' => 'Complementos de Veyon: control de acceso a internet, grabador de pantalla, chat, Auvidus, descubrimiento de red e integración con Microsoft Entra ID. Precios en pesos con actualizaciones incluidas.',
    'active'      => 'complementos',
];
$addonPrices = db_all('SELECT * FROM addon_prices ORDER BY sort_order, id');
require APP_ROOT . '/app/views/public_header.php';
?>


<!-- ============ CABECERA ============ -->
<section class="page-hero">
  <div class="container">
    <div class="hero-grid" style="align-items:center">
      <div class="reveal in">
        <span class="eyebrow">Complementos</span>
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem)">Funciones extra para <span class="grad-text">exigencias reales</span></h1>
        <p class="lead mt-2">Tu plan de licencia cubre la supervisión, el control remoto, las demostraciones y la transferencia de archivos. Los complementos añaden capas específicas para exámenes, comunicación y despliegues grandes: se contratan aparte y los mantenemos actualizados junto con el resto del sistema.</p>
        <div class="hero-actions mt-3">
          <a href="#complementos" class="btn btn-primary btn-lg">Ver los seis complementos</a>
          <a href="#precios" class="btn btn-ghost btn-lg">Consultar precios</a>
          <a href="index.php#contacto" class="btn btn-ghost btn-lg">Solicitar cotización</a>
        </div>
      </div>
      <div class="reveal in" data-d="2">
        <div class="frame" style="padding:2rem;background:linear-gradient(150deg,rgba(139,92,246,.1),rgba(34,211,238,.06))">
          <img src="assets/img/add-ons.png" alt="Ilustración de los complementos de Veyon">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ COMPLEMENTOS ============ -->
<section class="anchor" id="complementos" style="padding-top:clamp(2rem,4vw,3rem)">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Catálogo</span>
      <h2>Seis complementos <span class="grad-text">disponibles</span></h2>
      <p class="lead" style="margin-inline:auto">Se activan sobre tu instalación y aparecen integrados en el mismo panel del profesorado, sin herramientas ni ventanas adicionales.</p>
    </div>

    <div class="grid g-3">

      <article class="card reveal anchor" id="control-internet" data-d="1">
        <span class="tag">Exámenes</span>
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3c2.5 3 2.5 15 0 18M12 3c-2.5 3-2.5 15 0 18"></path><path d="M5 5l14 14"></path></svg></div>
        <h3>Control de acceso a internet</h3>
        <p>Corta o restablece la conexión a internet de toda la clase o de equipos concretos con un solo clic, muy útil durante pruebas y evaluaciones.</p>
        <ul>
          <li>Bloqueo total o por listas de sitios permitidos</li>
          <li>Aplicable al grupo completo o a una selección</li>
          <li>Restablecimiento inmediato al terminar</li>
        </ul>
        <a class="dl-link mt-2" href="https://docs.veyon.io/en/latest/addons/internet-access-control.html" target="_blank" rel="noopener">Documentación ↗</a>
      </article>

      <article class="card reveal anchor" id="grabador" data-d="2">
        <span class="tag">Evidencias</span>
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="14" height="12" rx="2"></rect><path d="M16 10l6-3v10l-6-3z"></path></svg></div>
        <h3>Grabador de pantalla</h3>
        <p>Graba en vídeo la actividad de los equipos para revisarla más tarde, por ejemplo ante sospechas de copia durante un examen.</p>
        <ul>
          <li>Grabación de uno o varios puestos a la vez</li>
          <li>Archivos de vídeo para análisis posterior</li>
          <li>Almacenamiento y retención configurables</li>
        </ul>
      </article>

      <article class="card reveal anchor" id="chat" data-d="3">
        <span class="tag">Comunicación</span>
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></div>
        <h3>Chat</h3>
        <p>Añade un canal de mensajería dentro del aula para resolver dudas por escrito sin interrumpir el trabajo del resto del grupo.</p>
        <ul>
          <li>Conversación con toda la clase o individual</li>
          <li>Consultas discretas sin levantar la mano</li>
          <li>Integrado en la interfaz de Veyon Master</li>
        </ul>
      </article>

      <article class="card reveal anchor" id="auvidus" data-d="1">
        <span class="tag">Dispositivos</span>
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5L6 9H2v6h4l5 4V5z"></path><path d="M22 9l-6 6M16 9l6 6"></path></svg></div>
        <h3>Auvidus</h3>
        <p>Gestiona audio, cámara web y dispositivos USB de los equipos: silencia altavoces y micrófonos o impide el uso de memorias externas mientras dure la prueba.</p>
        <ul>
          <li>Silenciado de salida de audio y micrófono</li>
          <li>Desactivación de la cámara web</li>
          <li>Bloqueo del almacenamiento USB</li>
        </ul>
      </article>

      <article class="card reveal anchor" id="entra-id" data-d="2">
        <span class="tag">Integración</span>
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l9 5v10l-9 5-9-5V7z"></path><path d="M12 22V12M21 7l-9 5-9-5"></path></svg></div>
        <h3>Conector para Microsoft Entra ID</h3>
        <p>Conecta Veyon con Microsoft Entra ID (antes Azure AD) para que los dispositivos, las ubicaciones y los grupos de seguridad aparezcan automáticamente en el panel.</p>
        <ul>
          <li>Sincronización de dispositivos y grupos</li>
          <li>Ubicaciones creadas sin listas manuales</li>
          <li>Ideal para centros ya integrados en Microsoft 365</li>
        </ul>
        <a class="dl-link mt-2" href="https://docs.veyon.io/en/latest/addons/entra-id-connector.html" target="_blank" rel="noopener">Documentación ↗</a>
      </article>

      <article class="card reveal anchor" id="descubrimiento" data-d="3">
        <span class="tag">Redes</span>
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M12 2v4M12 18v4M2 12h4M18 12h4M5 5l3 3M16 16l3 3M19 5l-3 3M8 16l-3 3"></path></svg></div>
        <h3>Descubrimiento de red</h3>
        <p>Rastrea las redes que definas en busca de equipos con el servicio de Veyon activo y rellena el panel de ubicaciones automáticamente.</p>
        <ul>
          <li>Exploración de rangos de red configurables</li>
          <li>Alta automática de equipos encontrados</li>
          <li>Menos mantenimiento manual de inventarios</li>
        </ul>
        <a class="dl-link mt-2" href="https://docs.veyon.io/en/latest/addons/network-discovery.html" target="_blank" rel="noopener">Documentación ↗</a>
      </article>

    </div>
  </div>
</section>

<!-- ============ DESCARGA DE COMPLEMENTOS ============ -->
<section class="section-alt anchor" id="descarga">
  <div class="container">
    <div class="section-head left reveal">
      <span class="eyebrow">Paquetes</span>
      <h2>Paquetes de complementos <span class="grad-text">4.11.2</span></h2>
      <p class="lead">Los paquetes se instalan sobre una instalación existente de Veyon. Si tu plan los incluye, mantenemos su licencia activa y su versión al día en cada equipo; si prefieres hacerlo por tu cuenta, aquí tienes los archivos oficiales.</p>
    </div>

    <div class="table-wrap reveal">
      <table class="dl-table">
        <thead><tr><th>Sistema</th><th>Formato</th><th>Paquete</th><th style="text-align:right">Descarga</th></tr></thead>
        <tbody>
          <tr><td><b>Windows</b> 10 / 11 / Server</td><td><span class="arch">exe · x86_64</span></td><td class="pkg">veyon-addons-4.11.2.0-win64-setup.exe</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons-4.11.2.0-win64-setup.exe">Descargar 64 bits</a></td></tr>
          <tr><td><b>Windows</b> (sistemas antiguos)</td><td><span class="arch">exe · x86</span></td><td class="pkg">veyon-addons-4.11.2.0-win32-setup.exe</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons-4.11.2.0-win32-setup.exe">Descargar 32 bits</a></td></tr>
          <tr><td><b>Debian 11</b></td><td><span class="arch">deb · amd64</span></td><td class="pkg">veyon-addons_4.11.2.0-debian.11_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons_4.11.2.0-debian.11_amd64.deb">Descargar .deb</a></td></tr>
          <tr><td><b>Debian 12</b></td><td><span class="arch">deb · amd64</span></td><td class="pkg">veyon-addons_4.11.2.0-debian.12_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons_4.11.2.0-debian.12_amd64.deb">Descargar .deb</a></td></tr>
          <tr><td><b>Debian 13</b></td><td><span class="arch">deb · amd64</span></td><td class="pkg">veyon-addons_4.11.2.0-debian.13_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons_4.11.2.0-debian.13_amd64.deb">Descargar .deb</a></td></tr>
          <tr><td><b>Ubuntu 22.04 LTS</b></td><td><span class="arch">deb · amd64</span></td><td class="pkg">veyon-addons_4.11.2.0-ubuntu.22.04_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons_4.11.2.0-ubuntu.22.04_amd64.deb">Descargar .deb</a></td></tr>
          <tr><td><b>Ubuntu 24.04 LTS</b></td><td><span class="arch">deb · amd64</span></td><td class="pkg">veyon-addons_4.11.2.0-ubuntu.24.04_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons_4.11.2.0-ubuntu.24.04_amd64.deb">Descargar .deb</a></td></tr>
          <tr><td><b>Ubuntu 26.04 LTS</b></td><td><span class="arch">deb · amd64</span></td><td class="pkg">veyon-addons_4.11.2.0-ubuntu.26.04_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons_4.11.2.0-ubuntu.26.04_amd64.deb">Descargar .deb</a></td></tr>
          <tr><td><b>Fedora 43</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-addons-4.11.2.0-fedora.43.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons-4.11.2.0-fedora.43.x86_64.rpm">Descargar .rpm</a></td></tr>
          <tr><td><b>Fedora 44</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-addons-4.11.2.0-fedora.44.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons-4.11.2.0-fedora.44.x86_64.rpm">Descargar .rpm</a></td></tr>
          <tr><td><b>openSUSE Leap 16.0</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-addons-4.11.2.0-opensuse.leap.16.0.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons-4.11.2.0-opensuse.leap.16.0.x86_64.rpm">Descargar .rpm</a></td></tr>
          <tr><td><b>openSUSE Tumbleweed</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-addons-4.11.2.0-opensuse.tumbleweed.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons-4.11.2.0-opensuse.tumbleweed.x86_64.rpm">Descargar .rpm</a></td></tr>
          <tr><td><b>RHEL / Rocky Linux 8</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-addons-4.11.2.0-rhel.8.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons-4.11.2.0-rhel.8.x86_64.rpm">Descargar .rpm</a></td></tr>
          <tr><td><b>RHEL / Rocky Linux 9</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-addons-4.11.2.0-rhel.9.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons-4.11.2.0-rhel.9.x86_64.rpm">Descargar .rpm</a></td></tr>
          <tr><td><b>RHEL / Rocky Linux 10</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-addons-4.11.2.0-rhel.10.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/addons/releases/download/v4.11.2/veyon-addons-4.11.2.0-rhel.10.x86_64.rpm">Descargar .rpm</a></td></tr>
        </tbody>
      </table>
    </div>

    <div class="flex mt-3">
      <a class="dl-link" href="https://github.com/veyon/addons/releases/tag/v4.11.2" target="_blank" rel="noopener">Notas de la versión de complementos ↗</a>
      <span class="muted small">· Si tu plan incluye complementos, mantenemos su licencia activa y su versión al día en cada equipo.</span>
    </div>
  </div>
</section>

<!-- ============ PRECIOS ============ -->
<section class="anchor" id="precios">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Licencias</span>
      <h2>Precios de complementos <span class="grad-text">por tipo de organización</span></h2>
      <p class="lead" style="margin-inline:auto">Valores anuales en <span data-cur-code>COP</span>, sin IVA, con actualizaciones y soporte incluidos. Cambia la moneda en la barra superior. Para secretarías de educación, distritos y empresas preparamos una propuesta a medida.</p>
    </div>

    <div class="table-wrap reveal">
      <table class="price-table">
        <thead>
          <tr>
            <th>Tipo de organización</th>
            <th>Un complemento<br><span style="text-transform:none;letter-spacing:0">licencia anual</span></th>
            <th>Paquete completo<br><span style="text-transform:none;letter-spacing:0">licencia anual</span></th>
            <th>Incluye</th>
          </tr>
        </thead>
        <tbody>
<?php foreach ($addonPrices as $row): ?>
          <tr>
            <th scope="row"><?= e($row['org_type']) ?></th>
<?php if ($row['custom_quote']): ?>
            <td colspan="3" class="muted"><?= e($row['includes'] ?: 'Propuesta a medida') ?> — <a href="index.php#contacto" class="link">solicita una cotización</a></td>
<?php else: ?>
            <td><?php if ($row['single_price'] !== null): ?><b data-cop="<?= e(num_input($row['single_price'])) ?>"><?= e(money($row['single_price'])) ?></b><?php else: ?><span class="muted">A consultar</span><?php endif; ?></td>
            <td><?php if ($row['bundle_price'] !== null): ?><b data-cop="<?= e(num_input($row['bundle_price'])) ?>"><?= e(money($row['bundle_price'])) ?></b><?php else: ?><span class="muted">A consultar</span><?php endif; ?></td>
            <td class="muted small"><?= e($row['includes']) ?></td>
<?php endif; ?>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="grid g-3 mt-4">
      <div class="panel reveal" data-d="1">
        <h3><span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v8M8 12h8"></path><circle cx="12" cy="12" r="10"></circle></svg></span>Licencia anual</h3>
        <p>Vigencia de doce meses con actualizaciones incluidas mientras esté activa. Se renueva junto con el plan principal, en una sola factura.</p>
      </div>
      <div class="panel reveal" data-d="2">
        <h3><span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6H4a2 2 0 0 0-2 2v10h20V8a2 2 0 0 0-2-2zM8 6V4h8v2"></path></svg></span>Paquete completo</h3>
        <p>Incluye todos los complementos disponibles por un valor inferior al de contratarlos por separado. Es la opción habitual en instituciones de más de 50 equipos.</p>
      </div>
      <div class="panel reveal" data-d="3">
        <h3><span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6M1 20v-6h6"></path><path d="M3.5 9a9 9 0 0 1 14.9-3.4L23 10M1 14l4.6 4.4A9 9 0 0 0 20.5 15"></path></svg></span>Siempre actualizados</h3>
        <p>Mantenemos cada complemento en su última versión y con la licencia activa en todos los equipos, y enseñamos al profesorado a sacarle partido en clase.</p>
      </div>
    </div>

    <div class="cta mt-5 reveal">
      <span class="eyebrow">Solicitar presupuesto</span>
      <h2>¿Necesitas una <span class="grad-text">oferta para tu centro</span>?</h2>
      <p>Envíanos el número de equipos y los complementos que te interesan y recibirás una propuesta con precio cerrado, vigencia anual y forma de pago.</p>
      <div class="hero-actions">
        <a class="btn btn-primary btn-lg" href="index.php#contacto">Solicitar cotización</a>
        <a class="btn btn-ghost btn-lg" href="index.php#planes">Ver planes de licencia</a>
      </div>
    </div>
  </div>
</section>

<?php require APP_ROOT . '/app/views/public_footer.php'; ?>
