<?php
require __DIR__ . '/app/bootstrap.php';

$page = [
    'title'       => 'Política de reembolsos y cancelación — Veyon Control',
    'description' => 'Condiciones de retracto, reembolso y cancelación de las licencias de Veyon Control, operadas por Grupo Logic SAS Latinoamérica.',
    'active'      => '',
];
$legal = setting('legal_name', 'Grupo Logic SAS Latinoamérica');
$mail = setting('company_email', 'gestion@grupologiclatam.com');
$updated = setting('legal_updated_at', '2026-10-04');
require APP_ROOT . '/app/views/public_header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="legal-head">
      <span class="eyebrow">Documentos legales</span>
      <h1>Política de <span class="grad-text">reembolsos</span></h1>
      <p class="lead mt-2">Cuándo puedes pedir la devolución de tu dinero, cómo se solicita y en cuánto tiempo se resuelve.</p>
      <p class="small muted mt-2">Última actualización: <?= e(fdate($updated)) ?></p>
    </div>
  </div>
</section>

<section style="padding-top:0">
  <div class="container legal">
    <nav class="legal-nav" aria-label="Contenido">
      <a href="#alcance">1. Alcance</a>
      <a href="#retracto">2. Derecho de retracto</a>
      <a href="#garantia">3. Garantía de 30 días</a>
      <a href="#proporcional">4. Fallas del servicio</a>
      <a href="#excepciones">5. Excepciones</a>
      <a href="#solicitud">6. Cómo solicitarlo</a>
      <a href="#plazos">7. Plazos y medio</a>
      <a href="#renovaciones">8. Renovaciones</a>
      <a href="#reversion">9. Reversión del pago</a>
    </nav>

    <article class="legal-body">
      <h2 id="alcance">1. Alcance</h2>
      <p>Esta política aplica a todas las licencias y servicios contratados a <b><?= e($legal) ?></b> a través de <a class="link" href="<?= e(abs_url()) ?>">www.veyoncontrol.com</a>, pagados en línea o por transferencia. Forma parte de los <a class="link" href="<?= e(url('terminos.php')) ?>">términos y condiciones</a>.</p>

      <h2 id="retracto">2. Derecho de retracto</h2>
      <p>Si contratas como consumidor mediante comercio electrónico, puedes retractarte dentro de los <b>cinco (5) días hábiles</b> siguientes al pago, conforme al artículo 47 de la Ley 1480 de 2011, siempre que el servicio no haya comenzado a ejecutarse con tu consentimiento expreso. En ese caso devolvemos el <b>100 %</b> de lo pagado.</p>
      <p>Basta con escribirnos a <a class="link" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a> indicando el número de factura. No necesitas justificar el motivo.</p>

      <h2 id="garantia">3. Garantía de satisfacción de 30 días</h2>
      <p>Más allá del retracto legal, ofrecemos una garantía comercial voluntaria: si dentro de los <b>treinta (30) días calendario</b> siguientes al pago el servicio no cumple lo ofrecido y no logramos resolverlo, devolvemos el <b>100 %</b> de lo pagado, aunque la implantación ya haya iniciado.</p>
      <p>Para aplicarla solo pedimos que nos hayas dado la oportunidad de corregir el problema por el canal de soporte antes de la solicitud.</p>

      <h2 id="proporcional">4. Fallas del servicio durante la vigencia</h2>
      <p>Si después de los primeros 30 días el servicio deja de prestarse por causa atribuible a nosotros y no lo solucionamos en un plazo razonable, puedes terminar el contrato y recibir la devolución <b>proporcional al tiempo de vigencia no disfrutado</b>, calculada por días.</p>

      <h2 id="excepciones">5. Cuándo no procede el reembolso</h2>
      <ul>
        <li>Servicios ya prestados y aceptados, como jornadas de capacitación realizadas o despliegues completados, transcurrido el periodo de garantía de 30 días.</li>
        <li>Terminación por decisión del cliente después de los 30 días, cuando el servicio se está prestando con normalidad.</li>
        <li>Fallas originadas en la infraestructura del cliente: red, equipos, sistemas operativos no compatibles, políticas internas o software de terceros ajeno al plan.</li>
        <li>Imposibilidad de implantación porque el cliente no facilita los accesos o la información necesarios, tras requerimientos razonables.</li>
        <li>Incumplimiento grave de los términos de uso por parte del cliente.</li>
      </ul>
      <p>Si el servicio se prestó parcialmente, podemos descontar del reembolso el valor de lo efectivamente ejecutado, informándote el cálculo.</p>

      <h2 id="solicitud">6. Cómo solicitar un reembolso</h2>
      <ol class="legal-ol">
        <li>Escribe a <a class="link" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a> desde el correo registrado en tu cuenta.</li>
        <li>Indica el número de factura, la fecha del pago y el motivo de la solicitud.</li>
        <li>Confirmamos la recepción y damos respuesta en un máximo de <b>cinco (5) días hábiles</b>.</li>
        <li>Si la solicitud procede, iniciamos la devolución de inmediato.</li>
      </ol>

      <h2 id="plazos">7. Plazos y medio de devolución</h2>
      <p>El reembolso se realiza por el mismo medio empleado en el pago. Ordenamos la devolución dentro de los <b>diez (10) días hábiles</b> siguientes a la aprobación. El tiempo en que el dinero se ve reflejado depende del proveedor de pagos, del banco o del emisor de la tarjeta, y suele tardar entre 5 y 20 días hábiles adicionales. No cobramos cargos administrativos por tramitar un reembolso.</p>

      <h2 id="renovaciones">8. Renovaciones y cancelación</h2>
      <p>No realizamos cobros automáticos recurrentes: cada renovación requiere una compra o una factura aceptada por el cliente. Para no continuar con el servicio, basta con no renovar; la licencia caduca en su fecha de vencimiento. Si se emite una renovación por error, se anula y se devuelve el 100 % del valor pagado.</p>

      <h2 id="reversion">9. Reversión del pago</h2>
      <p>Cuando proceda conforme al artículo 51 de la Ley 1480 de 2011 (por ejemplo, fraude, operación no solicitada o servicio no prestado), puedes solicitar la reversión del pago ante tu entidad financiera y ante nosotros. Tramitaremos lo que nos corresponda dentro de los plazos legales.</p>

      <h2 id="contacto">10. Contacto</h2>
      <p><b><?= e($legal) ?></b><br>Correo: <a class="link" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a><br>Sitio: <a class="link" href="<?= e(abs_url()) ?>">www.veyoncontrol.com</a></p>
    </article>

    <div class="legal-links">
      <a class="btn btn-ghost" href="<?= e(url('terminos.php')) ?>">Términos y condiciones</a>
      <a class="btn btn-ghost" href="<?= e(url('privacidad.php')) ?>">Política de privacidad</a>
      <a class="btn btn-ghost" href="<?= e(url('index.php#contacto')) ?>">Contacto</a>
    </div>
  </div>
</section>

<?php require APP_ROOT . '/app/views/public_footer.php'; ?>
