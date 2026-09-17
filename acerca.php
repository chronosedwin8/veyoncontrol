<?php
require __DIR__ . '/app/bootstrap.php';

$page = [
    'title'       => 'Nosotros — Veyon Control',
    'description' => 'Qué es Veyon, cómo se compone, cómo protege el acceso a los equipos y qué cubre nuestro servicio de licenciamiento, actualizaciones y soporte.',
    'active'      => 'acerca',
];
require APP_ROOT . '/app/views/public_header.php';
?>


<section class="page-hero">
  <div class="container">
    <div class="section-head left reveal in" style="max-width:840px">
      <span class="eyebrow">Acerca del proyecto</span>
      <h1 style="font-size:clamp(2.2rem,5vw,3.6rem)">Qué es Veyon y <span class="grad-text">por qué importa</span></h1>
      <p class="lead mt-2">Veyon —de <em>Virtual Eye On Networks</em>— es una plataforma multiplataforma para supervisar y controlar equipos en red, pensada para el ámbito educativo: aulas de informática, laboratorios universitarios, formación técnica y sesiones a distancia. Nosotros la licenciamos, la mantenemos actualizada y la sostenemos en el tiempo dentro de tu institución.</p>
    </div>

    <div class="stats reveal in" data-d="1">
      <div class="stat"><b data-count="4.11" data-decimals="2">0</b><span>Versión estable</span></div>
      <div class="stat"><b data-count="4" data-suffix="">0</b><span>Planes de licenciamiento</span></div>
      <div class="stat"><b data-count="2" data-suffix=" SO">0</b><span>Windows y Linux</span></div>
      <div class="stat"><b data-count="30" data-suffix="+">0</b><span>Idiomas disponibles</span></div>
    </div>
  </div>
</section>

<section style="padding-top:clamp(2rem,4vw,3rem)">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Arquitectura</span>
      <h2>Cuatro piezas, <span class="grad-text">un sistema coherente</span></h2>
      <p class="lead" style="margin-inline:auto">Una sola instalación contiene todos los componentes; en cada equipo eliges cuáles se activan.</p>
    </div>

    <div class="grid g-4">
      <article class="card reveal" data-d="1">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="14" rx="2"></rect><path d="M8 22h8"></path></svg></div>
        <h3>Veyon Master</h3>
        <p>El panel del profesorado: miniaturas en vivo, control remoto, demostraciones, bloqueo y envío de archivos.</p>
      </article>
      <article class="card reveal" data-d="2">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-2.9 1.2 2 2 0 1 1-4 0 1.7 1.7 0 0 0-2.9-1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1A1.7 1.7 0 0 0 4 15a2 2 0 1 1 0-4 1.7 1.7 0 0 0 1.2-2.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1A1.7 1.7 0 0 0 11 4a2 2 0 1 1 4 0 1.7 1.7 0 0 0 2.9 1.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1A1.7 1.7 0 0 0 20 11a2 2 0 1 1 0 4z"></path></svg></div>
        <h3>Veyon Service</h3>
        <p>El servicio que corre en cada puesto y atiende, ya autenticadas, las peticiones del panel.</p>
      </article>
      <article class="card reveal" data-d="3">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h10"></path></svg></div>
        <h3>Veyon Configurator</h3>
        <p>La consola del administrador: claves, autenticación, red, directorio LDAP y ajustes del servicio.</p>
      </article>
      <article class="card reveal" data-d="4">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 17l6-6-6-6M12 19h8"></path></svg></div>
        <h3>Veyon CTL / CLI</h3>
        <p>La herramienta de línea de comandos para automatizar despliegues, claves e importación de aulas.</p>
      </article>
    </div>
  </div>
</section>

<section class="section-alt">
  <div class="container">
    <div class="feature-row" style="margin-bottom:0">
      <div class="fr-text reveal">
        <span class="eyebrow">Seguridad</span>
        <h2>Acceso controlado, <span class="grad-text">tráfico cifrado</span></h2>
        <p>Una herramienta capaz de ver y manejar los equipos del aula solo tiene sentido si el acceso está bien acotado. Veyon cifra la comunicación con TLS y no autoriza ninguna conexión que no supere el método de autenticación configurado.</p>
        <ul class="fr-list">
          <li><span class="chk">✓</span><span><b>Autenticación por clave:</b> cada organización genera su pareja de claves; solo quien posee la privada puede conectarse.</span></li>
          <li><span class="chk">✓</span><span><b>Autenticación por credenciales:</b> validación del usuario contra el sistema o el directorio, limitada a un grupo autorizado.</span></li>
          <li><span class="chk">✓</span><span><b>Sin nube obligatoria:</b> todo el tráfico permanece dentro de la red del centro.</span></li>
          <li><span class="chk">✓</span><span><b>Código auditable:</b> cualquiera puede revisar qué hace el software y cómo lo hace.</span></li>
        </ul>
      </div>
      <div class="fr-media reveal" data-d="2">
        <div class="glow"></div>
        <div class="frame"><img src="assets/img/veyon-configurator-1.png" alt="Veyon Configurator con las opciones de autenticación y claves" loading="lazy"></div>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container" style="max-width:900px">
    <div class="section-head left reveal">
      <span class="eyebrow">Condiciones de uso</span>
      <h2>Contratación clara, <span class="grad-text">uso responsable</span></h2>
    </div>

    <details class="acc reveal" open>
      <summary>¿Qué estoy contratando exactamente?</summary>
      <div class="acc-body">Un servicio completo alrededor de Veyon: la licencia anual, las actualizaciones de versión en todos los puestos cubiertos, la capacitación del profesorado y el soporte durante la vigencia del plan. Lo que tu institución contrata es el servicio profesional completo que convierte la plataforma en una herramienta lista para usar en clase, con alguien que responde cuando algo falla. <a href="index.php#planes" class="link">Consulta los planes</a>.</div>
    </details>

    <details class="acc reveal">
      <summary>¿Puedo usarlo para vigilar a mi personal o sin avisar?</summary>
      <div class="acc-body">La plataforma está diseñada para actividad docente en aulas y laboratorios, y su uso debe ajustarse a la normativa de protección de datos y a las políticas de tu institución. Como regla general: informa a las personas afectadas, limita la supervisión a los equipos y horarios estrictamente docentes, y documenta quién tiene acceso al panel. Una instalación técnica correcta no sustituye a una política de uso escrita.</div>
    </details>

    <details class="acc reveal">
      <summary>¿Quién está detrás de Veyon Control?</summary>
      <div class="acc-body">Somos el equipo que licencia y sostiene Veyon en instituciones educativas: mantenemos cada puesto en la versión vigente, capacitamos al profesorado y atendemos el soporte durante toda la vigencia del plan.</div>
    </details>

    <details class="acc reveal">
      <summary>¿Qué pasa con mis datos y los de mis estudiantes?</summary>
      <div class="acc-body">La plataforma funciona dentro de la red de tu institución: las pantallas, los archivos y las sesiones no salen de tus equipos ni pasan por servidores nuestros. Al activar el plan definimos contigo quién tiene acceso al panel, en qué horarios y sobre qué aulas, y dejamos esa política documentada por escrito.</div>
    </details>

    <details class="acc reveal">
      <summary>¿Dónde consulto el aviso legal y la política de privacidad?</summary>
      <div class="acc-body">
        En las páginas oficiales del proyecto:
        <ul>
          <li><a href="https://veyon.io/en/imprint/" target="_blank" rel="noopener" class="link">Aviso legal</a></li>
          <li><a href="https://veyon.io/en/privacy/" target="_blank" rel="noopener" class="link">Política de privacidad</a></li>
          <li><a href="https://veyon.io/en/terms/" target="_blank" rel="noopener" class="link">Términos y condiciones</a></li>
        </ul>
      </div>
    </details>

    <div class="cta mt-5 reveal">
      <h2>Empieza por una <span class="grad-text">demostración</span></h2>
      <p>Te mostramos Veyon funcionando sobre tus propios equipos, sin costo y sin compromiso. Si encaja, te enviamos la propuesta con precio cerrado.</p>
      <div class="hero-actions">
        <a class="btn btn-primary btn-lg" href="index.php#contacto">Solicitar demostración</a>
        <a class="btn btn-ghost btn-lg" href="index.php#planes">Ver planes y precios</a>
      </div>
    </div>
  </div>
</section>

<?php require APP_ROOT . '/app/views/public_footer.php'; ?>
