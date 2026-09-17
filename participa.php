<?php
require __DIR__ . '/app/bootstrap.php';

$page = [
    'title'       => 'Soporte y recursos — Veyon Control',
    'description' => 'Soporte, capacitación continua, documentación y video guía para instituciones con un plan de Veyon Control activo.',
    'active'      => 'participa',
];
require APP_ROOT . '/app/views/public_header.php';
?>


<section class="page-hero">
  <div class="container">
    <div class="section-head left reveal in" style="max-width:840px">
      <span class="eyebrow">Soporte y recursos</span>
      <h1 style="font-size:clamp(2.2rem,5vw,3.6rem)">El acompañamiento <span class="grad-text">no termina el día de la instalación</span></h1>
      <p class="lead mt-2">Durante la vigencia de tu plan tienes canal directo de soporte, capacitación cuando entra profesorado nuevo y actualizaciones aplicadas en todos tus puestos. Aquí reúnes todo lo que necesitas para el día a día.</p>
    </div>

    <div class="assurance reveal in" data-d="1">
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg><div><b>48 horas hábiles</b><span>Tiempo de respuesta comprometido</span></div></div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg><div><b>Canal directo</b><span>Con el equipo que atiende tu institución</span></div></div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg><div><b>Capacitación continua</b><span>Refuerzos durante todo el año</span></div></div>
      <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg><div><b>Actualizaciones al día</b><span>Cada nueva versión en todos tus puestos</span></div></div>
    </div>
  </div>
</section>

<section style="padding-top:clamp(1rem,3vw,2rem)">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Recursos</span>
      <h2>Todo a mano para <span class="grad-text">el día a día del aula</span></h2>
    </div>

    <div class="grid g-3">

      <article class="card reveal" data-d="1">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="14" height="12" rx="2"></rect><path d="M16 10l6-3v10l-6-3z"></path></svg></div>
        <h3>Video guía</h3>
        <p>Recorrido completo por la instalación y el uso diario. Es el material que entregamos al profesorado como repaso después de la capacitación.</p>
        <a class="dl-link mt-2" href="index.php#video">Ver el video</a>
      </article>

      <article class="card reveal" data-d="2">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg></div>
        <h3>Documentación técnica</h3>
        <p>Referencia detallada de cada función, parámetro de configuración y opción de línea de comandos, para tu área de TI.</p>
        <a class="dl-link mt-2" href="https://docs.veyon.io/es/latest/" target="_blank" rel="noopener">Abrir documentación ↗</a>
      </article>

      <article class="card reveal" data-d="3">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></div>
        <h3>Soporte de tu plan</h3>
        <p>Escríbenos con el detalle de la incidencia y el equipo afectado. Respondemos en 48 horas hábiles, con prioridad según tu plan.</p>
        <a class="dl-link mt-2" href="index.php#contacto">Abrir un caso</a>
      </article>

      <article class="card reveal" data-d="1">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.9"></path></svg></div>
        <h3>Capacitación a demanda</h3>
        <p>Si entra profesorado nuevo o abres un aula más, coordinamos una sesión adicional sin costo dentro de la vigencia del plan.</p>
        <a class="dl-link mt-2" href="index.php#contacto">Solicitar sesión</a>
      </article>

      <article class="card reveal" data-d="2">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg></div>
        <h3>Políticas de uso</h3>
        <p>Plantillas para documentar quién supervisa, en qué horarios y sobre qué aulas, alineadas con la normativa de protección de datos.</p>
        <a class="dl-link mt-2" href="index.php#contacto">Pedir plantillas</a>
      </article>

      <article class="card reveal" data-d="3">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7z"></path></svg></div>
        <h3>Foro de usuarios</h3>
        <p>Comunidad internacional de administradores donde consultar casos poco frecuentes y configuraciones avanzadas.</p>
        <a class="dl-link mt-2" href="https://veyon.nodebb.com" target="_blank" rel="noopener">Entrar al foro ↗</a>
      </article>

    </div>
  </div>
</section>

<section class="section-alt">
  <div class="container">
    <div class="hero-grid" style="align-items:center">
      <div class="reveal">
        <span class="eyebrow">Reportar una incidencia</span>
        <h2>Un buen reporte <span class="grad-text">acorta la solución</span></h2>
        <p class="lead mb-3">Cuando algo falla, envíanos estos datos junto con la descripción. Con ellos solemos identificar la causa sin necesidad de una visita.</p>
        <ul class="fr-list">
          <li><span class="chk">✓</span><span>Aula y equipos afectados, y si el fallo es en uno o en todos.</span></li>
          <li><span class="chk">✓</span><span>Pasos para reproducirlo, en orden.</span></li>
          <li><span class="chk">✓</span><span>Qué esperabas que ocurriera y qué ocurrió en realidad.</span></li>
          <li><span class="chk">✓</span><span>Salida de los comandos de diagnóstico y capturas si es visual.</span></li>
        </ul>
        <a class="btn btn-primary mt-2" href="index.php#contacto">Abrir un caso de soporte</a>
      </div>

      <div class="reveal" data-d="2">
        <div class="terminal">
          <header><i></i><i></i><i></i><small>diagnóstico</small><button class="copy-btn" data-copy="#cmd-diag">Copiar</button></header>
          <pre id="cmd-diag"><span class="c-m"># Versión instalada</span>
<span class="c-p">$</span> veyon-ctl <span class="c-c">-v</span>

<span class="c-m"># Estado del servicio</span>
<span class="c-p">$</span> veyon-ctl service <span class="c-c">status</span>

<span class="c-m"># Volcado de la configuración actual</span>
<span class="c-p">$</span> veyon-ctl config <span class="c-c">list</span>

<span class="c-m"># Comprobar la conexión con un puesto</span>
<span class="c-p">$</span> veyon-ctl remoteaccess <span class="c-c">view</span> aula1-pc07</pre>
        </div>
        <p class="small muted mt-2">Copia la salida y adjúntala al caso: acorta mucho el diagnóstico.</p>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="cta reveal">
      <span class="eyebrow">Estamos disponibles</span>
      <h2>¿Necesitas ayuda <span class="grad-text">ahora mismo</span>?</h2>
      <p>Si tu institución todavía no tiene un plan activo, escríbenos igual: revisamos tu caso y te decimos qué haría falta para tenerlo al día.</p>
      <div class="hero-actions">
        <a class="btn btn-primary btn-lg" href="index.php#contacto">Hablar con soporte</a>
        <a class="btn btn-ghost btn-lg" href="index.php#planes">Ver planes de licencia</a>
      </div>
    </div>
  </div>
</section>

<?php require APP_ROOT . '/app/views/public_footer.php'; ?>
