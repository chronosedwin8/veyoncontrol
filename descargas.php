<?php
require __DIR__ . '/app/bootstrap.php';

$page = [
    'title'       => 'Descargas — Veyon Control',
    'description' => 'Instaladores de Veyon 4.11.2 para Windows y Linux, con guía de despliegue. Con un plan activo mantenemos cada puesto en la última versión durante todo el año.',
    'active'      => 'descargas',
];
require APP_ROOT . '/app/views/public_header.php';
?>


<!-- ============ CABECERA ============ -->
<section class="page-hero">
  <div class="container">
    <div class="section-head left reveal in" style="max-width:820px">
      <span class="eyebrow">Descargas · Versión 4.11.2</span>
      <h1 style="font-size:clamp(2.2rem,5vw,3.6rem)">Instala Veyon en <span class="grad-text">todos tus equipos</span></h1>
      <p class="lead mt-2">Estos son los paquetes oficiales publicados en GitHub, enlazados directamente desde su origen. Con un plan activo no tienes que estar pendiente de esta página: llevamos cada nueva versión a todos tus puestos y capacitamos al equipo docente. <a href="index.php#planes" class="link">Ver planes de licencia</a>.</p>
    </div>

    <div class="flex mb-4 reveal in" data-d="1">
      <span class="badge">Versión estable 4.11.2</span>
      <span class="badge violet">Publicada el 26 de agosto de 2026</span>
      <span class="badge amber">Windows y Linux</span>
      <a class="dl-link" href="https://github.com/veyon/veyon/releases/tag/v4.11.2" target="_blank" rel="noopener">Notas de esta versión ↗</a>
    </div>

    <!-- Detección automática de SO -->
    <div class="os-detect reveal in" data-d="2" id="os-detect">
      <div class="os-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"></rect><path d="M8 21h8M12 17v4"></path></svg>
      </div>
      <div class="txt">
        <b data-os-name>Detectando tu sistema…</b>
        <span data-os-desc>Un momento, estamos comprobando qué paquete te conviene.</span>
      </div>
      <a class="btn btn-primary" data-os-btn href="#windows">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        <span>Ver todos los paquetes</span>
      </a>
    </div>
  </div>
</section>

<!-- ============ FRANJA COMERCIAL ============ -->
<section style="padding:clamp(1.5rem,3vw,2.5rem) 0">
  <div class="container">
    <div class="assurance reveal">
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg><div><b>Actualizaciones incluidas</b><span>Cada puesto en la última versión</span></div></div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg><div><b>Respuesta en 48 h</b><span>Soporte durante toda la vigencia</span></div></div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg><div><b>Seguridad configurada</b><span>Claves, red y permisos revisados</span></div></div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg><div><b>Capacitación docente</b><span>Para que se use desde el primer día</span></div></div>
    </div>
  </div>
</section>

<!-- ============ WINDOWS ============ -->
<section class="anchor" id="windows" style="padding-top:clamp(2rem,4vw,3rem)">
  <div class="container">
    <div class="section-head left reveal">
      <span class="eyebrow">Windows</span>
      <h2>Instaladores para <span class="grad-text">Windows</span></h2>
      <p class="lead">Un único instalador incluye el panel del profesorado (Veyon Master), el servicio para los puestos del alumnado y el configurador. Durante la instalación eliges qué componentes activar en cada equipo.</p>
    </div>

    <div class="table-wrap reveal">
      <table class="dl-table">
        <thead>
          <tr><th>Sistema</th><th>Arquitectura</th><th>Paquete</th><th style="text-align:right">Descarga</th></tr>
        </thead>
        <tbody>
          <tr>
            <td><b>Windows 10 / 11 / Server</b></td>
            <td><span class="arch">x86_64</span></td>
            <td class="pkg">veyon-4.11.2.0-win64-setup.exe</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon-4.11.2.0-win64-setup.exe">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              Descargar 64 bits</a></td>
          </tr>
          <tr>
            <td><b>Windows (sistemas antiguos)</b></td>
            <td><span class="arch">x86 / 32 bits</span></td>
            <td class="pkg">veyon-4.11.2.0-win32-setup.exe</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon-4.11.2.0-win32-setup.exe">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              Descargar 32 bits</a></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="grid g-2 mt-4">
      <div class="panel reveal">
        <h3><span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 17l6-6-6-6M12 19h8"></path></svg></span>Instalación desatendida</h3>
        <p class="mb-2">Para desplegar en muchos puestos, ejecuta el instalador en modo silencioso desde tu script o GPO.</p>
        <div class="terminal">
          <header><i></i><i></i><i></i><small>cmd · despliegue</small><button class="copy-btn" data-copy="#cmd-win">Copiar</button></header>
          <pre id="cmd-win"><span class="c-m">:: Puesto del alumnado (sin panel de control)</span>
veyon-4.11.2.0-win64-setup.exe /S /NoMaster

<span class="c-m">:: Equipo del profesorado (con Veyon Master)</span>
veyon-4.11.2.0-win64-setup.exe /S

<span class="c-m">:: Desinstalación silenciosa</span>
"C:\Program Files\Veyon\uninstall.exe" /S</pre>
        </div>
      </div>

      <div class="panel reveal" data-d="1">
        <h3><span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></span>Antes de empezar</h3>
        <ul style="display:grid;gap:.7rem;color:var(--txt-dim);font-size:.94rem">
          <li>Instala primero el equipo del profesorado y genera allí la pareja de claves de acceso.</li>
          <li>Importa la clave pública en cada puesto del alumnado para autorizar las conexiones.</li>
          <li>Permite el tráfico del servicio Veyon (puerto TCP 11100 por defecto) en el cortafuegos.</li>
          <li>Si usas antivirus con control de aplicaciones, añade una excepción para el servicio.</li>
          <li>Reinicia el equipo tras la instalación para que el servicio arranque correctamente.</li>
        </ul>
        <a class="btn btn-ghost btn-sm mt-3" href="https://docs.veyon.io/es/latest/admin/installation.html" target="_blank" rel="noopener">Guía de instalación oficial ↗</a>
      </div>
    </div>
  </div>
</section>

<!-- ============ LINUX ============ -->
<section class="section-alt anchor" id="linux">
  <div class="container">
    <div class="section-head left reveal">
      <span class="eyebrow">Linux</span>
      <h2>Paquetes para <span class="grad-text">Debian, Ubuntu y familia RPM</span></h2>
      <p class="lead">Elige el paquete que corresponda exactamente a tu distribución y versión. Usa el buscador para filtrar la tabla.</p>
    </div>

    <div class="flex mb-3 reveal">
      <input id="pkg-filter" type="search" placeholder="Filtrar por distribución, p. ej. ubuntu o fedora…"
        style="flex:1;min-width:260px;padding:.85rem 1.1rem;border-radius:999px;border:1px solid var(--stroke);background:var(--surface);outline:none">
      <span class="badge violet">13 paquetes disponibles</span>
    </div>

    <div class="table-wrap reveal" data-filterable>
      <table class="dl-table">
        <thead>
          <tr><th>Distribución</th><th>Formato</th><th>Paquete</th><th style="text-align:right">Descarga</th></tr>
        </thead>
        <tbody>
          <tr>
            <td><b>Debian 11</b> (Bullseye)</td><td><span class="arch">deb · amd64</span></td>
            <td class="pkg">veyon_4.11.2.0-debian.11_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon_4.11.2.0-debian.11_amd64.deb">Descargar .deb</a></td>
          </tr>
          <tr>
            <td><b>Debian 12</b> (Bookworm)</td><td><span class="arch">deb · amd64</span></td>
            <td class="pkg">veyon_4.11.2.0-debian.12_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon_4.11.2.0-debian.12_amd64.deb">Descargar .deb</a></td>
          </tr>
          <tr>
            <td><b>Debian 13</b> (Trixie)</td><td><span class="arch">deb · amd64</span></td>
            <td class="pkg">veyon_4.11.2.0-debian.13_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon_4.11.2.0-debian.13_amd64.deb">Descargar .deb</a></td>
          </tr>
          <tr>
            <td><b>Ubuntu 22.04 LTS</b></td><td><span class="arch">deb · amd64</span></td>
            <td class="pkg">veyon_4.11.2.0-ubuntu.22.04_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon_4.11.2.0-ubuntu.22.04_amd64.deb">Descargar .deb</a></td>
          </tr>
          <tr>
            <td><b>Ubuntu 24.04 LTS</b></td><td><span class="arch">deb · amd64</span></td>
            <td class="pkg">veyon_4.11.2.0-ubuntu.24.04_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon_4.11.2.0-ubuntu.24.04_amd64.deb">Descargar .deb</a></td>
          </tr>
          <tr>
            <td><b>Ubuntu 26.04 LTS</b></td><td><span class="arch">deb · amd64</span></td>
            <td class="pkg">veyon_4.11.2.0-ubuntu.26.04_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon_4.11.2.0-ubuntu.26.04_amd64.deb">Descargar .deb</a></td>
          </tr>
          <tr>
            <td><b>Fedora 43</b></td><td><span class="arch">rpm · x86_64</span></td>
            <td class="pkg">veyon-4.11.2.0-fedora.43.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon-4.11.2.0-fedora.43.x86_64.rpm">Descargar .rpm</a></td>
          </tr>
          <tr>
            <td><b>Fedora 44</b></td><td><span class="arch">rpm · x86_64</span></td>
            <td class="pkg">veyon-4.11.2.0-fedora.44.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon-4.11.2.0-fedora.44.x86_64.rpm">Descargar .rpm</a></td>
          </tr>
          <tr>
            <td><b>openSUSE Leap 16.0</b></td><td><span class="arch">rpm · x86_64</span></td>
            <td class="pkg">veyon-4.11.2.0-opensuse.leap.16.0.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon-4.11.2.0-opensuse.leap.16.0.x86_64.rpm">Descargar .rpm</a></td>
          </tr>
          <tr>
            <td><b>openSUSE Tumbleweed</b></td><td><span class="arch">rpm · x86_64</span></td>
            <td class="pkg">veyon-4.11.2.0-opensuse.tumbleweed.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon-4.11.2.0-opensuse.tumbleweed.x86_64.rpm">Descargar .rpm</a></td>
          </tr>
          <tr>
            <td><b>RHEL / Rocky Linux 8</b></td><td><span class="arch">rpm · x86_64</span></td>
            <td class="pkg">veyon-4.11.2.0-rhel.8.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon-4.11.2.0-rhel.8.x86_64.rpm">Descargar .rpm</a></td>
          </tr>
          <tr>
            <td><b>RHEL / Rocky Linux 9</b></td><td><span class="arch">rpm · x86_64</span></td>
            <td class="pkg">veyon-4.11.2.0-rhel.9.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon-4.11.2.0-rhel.9.x86_64.rpm">Descargar .rpm</a></td>
          </tr>
          <tr>
            <td><b>RHEL / Rocky Linux 10</b></td><td><span class="arch">rpm · x86_64</span></td>
            <td class="pkg">veyon-4.11.2.0-rhel.10.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.11.2/veyon-4.11.2.0-rhel.10.x86_64.rpm">Descargar .rpm</a></td>
          </tr>
        </tbody>
      </table>
    </div>
    <p id="pkg-empty" class="muted small mt-2" style="display:none">No hay paquetes que coincidan con la búsqueda. Prueba con otro término o escríbenos y te indicamos el paquete correcto.</p>

    <div class="grid g-2 mt-4">
      <div class="panel reveal">
        <h3><span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"></path></svg></span>Repositorio PPA para Ubuntu</h3>
        <p class="mb-2">El proyecto mantiene un repositorio PPA con compilaciones para i386, amd64, armhf y arm64, con actualizaciones automáticas a través del gestor de paquetes.</p>
        <div class="terminal">
          <header><i></i><i></i><i></i><small>bash · ubuntu</small><button class="copy-btn" data-copy="#cmd-ppa">Copiar</button></header>
          <pre id="cmd-ppa"><span class="c-p">$</span> sudo add-apt-repository ppa:veyon/stable
<span class="c-p">$</span> sudo apt update
<span class="c-p">$</span> sudo apt install <span class="c-c">veyon</span></pre>
        </div>
        <a class="btn btn-ghost btn-sm mt-3" href="https://launchpad.net/~veyon/+archive/ubuntu/stable/" target="_blank" rel="noopener">Ver el PPA en Launchpad ↗</a>
      </div>

      <div class="panel reveal" data-d="1">
        <h3><span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h10"></path></svg></span>Instalar el paquete descargado</h3>
        <p class="mb-2">Instala el archivo con el gestor de tu distribución para que resuelva las dependencias automáticamente.</p>
        <div class="terminal">
          <header><i></i><i></i><i></i><small>bash · deb / rpm</small><button class="copy-btn" data-copy="#cmd-lin">Copiar</button></header>
          <pre id="cmd-lin"><span class="c-m"># Debian / Ubuntu</span>
<span class="c-p">$</span> sudo apt install ./veyon_4.11.2.0-ubuntu.24.04_amd64.deb

<span class="c-m"># Fedora</span>
<span class="c-p">$</span> sudo dnf install ./veyon-4.11.2.0-fedora.43.x86_64.rpm

<span class="c-m"># openSUSE</span>
<span class="c-p">$</span> sudo zypper install ./veyon-4.11.2.0-opensuse.leap.16.0.x86_64.rpm</pre>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ VERSIONES ANTERIORES ============ -->
<section class="section-alt anchor" id="antiguas">
  <div class="container">
    <div class="section-head left reveal">
      <span class="eyebrow">Sistemas antiguos</span>
      <h2>Paquetes heredados <span class="grad-text">(versión 4.1.92)</span></h2>
      <p class="lead">Para distribuciones que ya no reciben compilaciones actuales, el proyecto mantiene disponibles los paquetes de la versión 4.1.92. Se recomiendan únicamente si no puedes actualizar el sistema operativo.</p>
    </div>

    <div class="table-wrap reveal">
      <table class="dl-table">
        <thead><tr><th>Distribución</th><th>Formato</th><th>Paquete</th><th style="text-align:right">Descarga</th></tr></thead>
        <tbody>
          <tr><td><b>Debian 9</b> (Stretch)</td><td><span class="arch">deb · amd64</span></td><td class="pkg">veyon_4.1.92-debian-stretch_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.1.92/veyon_4.1.92-debian-stretch_amd64.deb">Descargar</a></td></tr>
          <tr><td><b>Debian 10</b> (Buster)</td><td><span class="arch">deb · amd64</span></td><td class="pkg">veyon_4.1.92-debian-buster_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.1.92/veyon_4.1.92-debian-buster_amd64.deb">Descargar</a></td></tr>
          <tr><td><b>Ubuntu 16.04</b> (Xenial)</td><td><span class="arch">deb · amd64</span></td><td class="pkg">veyon_4.1.92-ubuntu-xenial_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.1.92/veyon_4.1.92-ubuntu-xenial_amd64.deb">Descargar</a></td></tr>
          <tr><td><b>Ubuntu 18.04</b> (Bionic)</td><td><span class="arch">deb · amd64</span></td><td class="pkg">veyon_4.1.92-ubuntu-bionic_amd64.deb</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.1.92/veyon_4.1.92-ubuntu-bionic_amd64.deb">Descargar</a></td></tr>
          <tr><td><b>CentOS 7.4</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-4.1.92.centos-74.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.1.92/veyon-4.1.92.centos-74.x86_64.rpm">Descargar</a></td></tr>
          <tr><td><b>Fedora 28</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-4.1.92.fc28.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.1.92/veyon-4.1.92.fc28.x86_64.rpm">Descargar</a></td></tr>
          <tr><td><b>openSUSE Leap 42.3</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-4.1.92.opensuse-42.3.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.1.92/veyon-4.1.92.opensuse-42.3.x86_64.rpm">Descargar</a></td></tr>
          <tr><td><b>openSUSE Leap 15.0</b></td><td><span class="arch">rpm · x86_64</span></td><td class="pkg">veyon-4.1.92.opensuse-15.0.x86_64.rpm</td>
            <td style="text-align:right"><a class="dl-link" href="https://github.com/veyon/veyon/releases/download/v4.1.92/veyon-4.1.92.opensuse-15.0.x86_64.rpm">Descargar</a></td></tr>
        </tbody>
      </table>
    </div>
    <p class="muted small mt-2">Todas las versiones publicadas, con sus notas de cambios, están disponibles en la <a href="https://github.com/veyon/veyon/releases" target="_blank" rel="noopener" class="link">página de lanzamientos del proyecto</a>.</p>
  </div>
</section>

<!-- ============ REQUISITOS ============ -->
<section>
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Requisitos</span>
      <h2>Qué necesitas <span class="grad-text">para funcionar</span></h2>
    </div>

    <div class="grid g-3">
      <article class="card reveal" data-d="1">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="14" rx="2"></rect><path d="M8 22h8"></path></svg></div>
        <h3>Sistema operativo</h3>
        <ul>
          <li>Windows 10, Windows 11 o Windows Server</li>
          <li>Distribuciones Linux con paquete disponible</li>
          <li>Equipos Windows y Linux en la misma aula</li>
        </ul>
      </article>

      <article class="card reveal" data-d="2">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5v14"></path><circle cx="12" cy="12" r="10"></circle></svg></div>
        <h3>Red</h3>
        <ul>
          <li>Red local con visibilidad entre los equipos</li>
          <li>Puerto TCP 11100 abierto en el cortafuegos</li>
          <li>Sin necesidad de acceso a internet</li>
        </ul>
      </article>

      <article class="card reveal" data-d="3">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8v10H3z"></path></svg></div>
        <h3>Permisos</h3>
        <ul>
          <li>Cuenta con privilegios de administrador para instalar</li>
          <li>Pareja de claves o grupo de usuarios autorizado</li>
          <li>Opcional: acceso al directorio LDAP o AD del centro</li>
        </ul>
      </article>
    </div>

    <div class="cta mt-5 reveal">
      <span class="eyebrow">Siempre en la última versión</span>
      <h2>¿Y cuando salga la <span class="grad-text">próxima versión</span>?</h2>
      <p>Con un plan activo no tienes que volver aquí: llevamos cada actualización a todos tus puestos, verificamos que quedaron al día, capacitamos al profesorado y respondemos el soporte durante los doce meses de vigencia.</p>
      <div class="hero-actions">
        <a class="btn btn-primary btn-lg" href="index.php#planes">Ver planes y precios</a>
        <a class="btn btn-ghost btn-lg" href="index.php#contacto">Solicitar cotización</a>
        <a class="btn btn-ghost btn-lg" href="index.php#video">Ver el video guía</a>
      </div>
    </div>
  </div>
</section>

<?php require APP_ROOT . '/app/views/public_footer.php'; ?>
