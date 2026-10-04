<?php
require __DIR__ . '/app/bootstrap.php';

$page = [
    'title'       => 'Términos y condiciones del servicio — Veyon Control',
    'description' => 'Términos y condiciones de contratación de las licencias y servicios de Veyon Control, operados por Grupo Logic SAS Latinoamérica.',
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
      <h1>Términos y condiciones <span class="grad-text">del servicio</span></h1>
      <p class="lead mt-2">Estas condiciones regulan la contratación y el uso de los servicios de licenciamiento, actualización, capacitación y soporte que <?= e($legal) ?> ofrece bajo la marca Veyon Control.</p>
      <p class="small muted mt-2">Última actualización: <?= e(fdate($updated)) ?></p>
    </div>
  </div>
</section>

<section style="padding-top:0">
  <div class="container legal">
    <nav class="legal-nav" aria-label="Contenido">
      <a href="#identificacion">1. Identificación</a>
      <a href="#objeto">2. Objeto</a>
      <a href="#aceptacion">3. Aceptación</a>
      <a href="#cuenta">4. Cuenta</a>
      <a href="#precios">5. Precios</a>
      <a href="#pago">6. Pago y comprobantes</a>
      <a href="#vigencia">7. Vigencia</a>
      <a href="#obligaciones">8. Obligaciones</a>
      <a href="#propiedad">9. Propiedad intelectual</a>
      <a href="#soporte">10. Soporte</a>
      <a href="#responsabilidad">11. Responsabilidad</a>
      <a href="#datos">12. Datos personales</a>
      <a href="#reembolsos">13. Reembolsos</a>
      <a href="#terminacion">14. Terminación</a>
      <a href="#ley">15. Ley aplicable</a>
    </nav>

    <article class="legal-body">
      <h2 id="identificacion">1. Identificación del prestador</h2>
      <p>El servicio es prestado por <b><?= e($legal) ?></b><?= setting('company_tax_id') ? ', identificada con NIT ' . e(setting('company_tax_id')) : '' ?>, sociedad por acciones simplificada constituida bajo las leyes de la República de Colombia, que opera comercialmente bajo la marca <b>Veyon Control</b> en el sitio <a class="link" href="<?= e(abs_url()) ?>">www.veyoncontrol.com</a>.</p>
      <p>Canal oficial de atención y notificaciones: <a class="link" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a>. Toda comunicación relativa a la contratación, el soporte, las garantías o el ejercicio de derechos se atiende por este medio.</p>

      <h2 id="objeto">2. Objeto y descripción del servicio</h2>
      <p>Veyon Control comercializa un servicio anual de implantación y acompañamiento sobre <b>Veyon</b>, software libre de gestión de aulas informáticas distribuido por sus autores bajo licencia GNU GPL versión 2. El servicio contratado comprende, según el plan adquirido:</p>
      <ul>
        <li>Licencia de uso del servicio por el número de equipos que cubre el plan y por la vigencia contratada.</li>
        <li>Instalación, configuración y actualización de versión en los puestos cubiertos.</li>
        <li>Capacitación del personal docente y del área de tecnología, en el alcance indicado en cada plan.</li>
        <li>Soporte técnico durante la vigencia, con los tiempos de respuesta publicados en el sitio.</li>
        <li>Verificación periódica del estado de actualización de los equipos cubiertos.</li>
      </ul>
      <p>Las características y los precios vigentes de cada plan son los publicados en la página de <a class="link" href="<?= e(url('index.php#planes')) ?>">planes y precios</a> en el momento de la compra.</p>

      <h2 id="aceptacion">3. Aceptación de los términos</h2>
      <p>Al crear una cuenta, solicitar una cotización o completar un pago, el cliente declara que ha leído y acepta estos términos, la <a class="link" href="<?= e(url('privacidad.php')) ?>">política de privacidad</a> y la <a class="link" href="<?= e(url('reembolsos.php')) ?>">política de reembolsos</a>. Quien contrata declara estar facultado para obligar a la institución que representa.</p>
      <p>El servicio está dirigido a instituciones educativas, empresas y entidades. No está dirigido a menores de edad.</p>

      <h2 id="cuenta">4. Registro, cuenta y credenciales</h2>
      <p>Para comprar en línea y consultar facturas, cotizaciones, pagos y licencias, el cliente crea una cuenta en el portal. El cliente es responsable de la veracidad de los datos registrados, de la custodia de sus credenciales y de las operaciones realizadas desde su cuenta. Si detecta un uso no autorizado debe informarlo de inmediato al correo de atención.</p>

      <h2 id="precios">5. Precios, moneda e impuestos</h2>
      <ul>
        <li>Los precios se publican en pesos colombianos (COP). El selector de moneda del sitio es informativo: el cobro se realiza en COP.</li>
        <li>Los valores corresponden a licencias anuales por institución, según el número de equipos del plan.</li>
        <li>Los impuestos aplicables se indican en la factura o en el resumen de compra antes del pago.</li>
        <li>Los precios pueden cambiar en cualquier momento. El precio aplicable es el vigente al momento de la compra o el indicado en una cotización dentro de su periodo de validez.</li>
      </ul>

      <h2 id="pago">6. Pago, facturación y comprobantes</h2>
      <p>El pago se realiza en línea a través de los proveedores de pago autorizados que se muestran en el momento de la compra. <?= e($legal) ?> no recibe ni almacena números de tarjeta ni credenciales bancarias: esos datos se tratan directamente por el proveedor de pagos en su propio entorno seguro.</p>
      <p>También se admite el pago por transferencia o consignación bancaria, previo acuerdo por el canal de atención.</p>
      <p>Una vez acreditado el pago, el cliente recibe en su portal el comprobante electrónico de la operación, a nombre de la institución, con el detalle del plan contratado, el número de equipos cubiertos, el valor y la vigencia. Cuando la operación lo requiera, se emite la factura correspondiente conforme a la normativa colombiana.</p>

      <h2 id="vigencia">7. Vigencia, activación y renovación</h2>
      <ul>
        <li>La vigencia es de <?= e(setting('license_months', '12')) ?> meses contados desde la activación de la licencia, que se produce al acreditarse el pago.</li>
        <li>No existen cobros automáticos recurrentes: la renovación se realiza únicamente mediante una nueva compra o factura aceptada por el cliente.</li>
        <li>Antes del vencimiento se avisa al cliente para que decida si renueva. Si no renueva, la licencia caduca en la fecha de vencimiento y el acompañamiento se interrumpe.</li>
        <li>Si el número de equipos supera el del plan contratado, se ajusta al plan siguiente y se cobra la diferencia proporcional al tiempo restante de vigencia.</li>
      </ul>

      <h2 id="obligaciones">8. Obligaciones y uso aceptable</h2>
      <p>El cliente se obliga a:</p>
      <ul>
        <li>Usar el servicio dentro de su propia infraestructura y únicamente en los equipos cubiertos por el plan.</li>
        <li>Disponer de la infraestructura mínima (red, sistemas operativos compatibles y permisos de administración) necesaria para la implantación.</li>
        <li>Informar a su comunidad educativa sobre el uso de herramientas de supervisión en el aula y cumplir la normativa de protección de datos que le resulte aplicable.</li>
        <li>No emplear el servicio para vigilancia encubierta de personas, ni para fines ilícitos o ajenos a la actividad académica o institucional.</li>
        <li>No revender, sublicenciar ni ceder el servicio a terceros sin autorización escrita.</li>
      </ul>
      <p>El incumplimiento de estas obligaciones puede dar lugar a la suspensión del servicio, previa comunicación al cliente.</p>

      <h2 id="propiedad">9. Propiedad intelectual</h2>
      <p>Veyon es software libre de terceros, distribuido bajo licencia GNU GPL v2; sus derechos pertenecen a sus respectivos autores y su uso se rige por dicha licencia. Lo que el cliente contrata con <?= e($legal) ?> es un servicio profesional de licenciamiento gestionado, implantación, actualización, capacitación y soporte, no la venta del software de terceros.</p>
      <p>Los materiales de capacitación, la documentación propia, la marca Veyon Control y el contenido del sitio son propiedad de <?= e($legal) ?> y se licencian al cliente solo para uso interno durante la vigencia.</p>

      <h2 id="soporte">10. Soporte y niveles de servicio</h2>
      <p>El soporte se presta en español por el canal de correo indicado, en días hábiles. El tiempo objetivo de primera respuesta es de 48 horas hábiles, y menor en los planes con soporte prioritario o dedicado. El soporte cubre el funcionamiento del servicio contratado; no cubre fallas de la infraestructura del cliente, de su red o de software de terceros no incluido en el plan.</p>

      <h2 id="responsabilidad">11. Garantías y limitación de responsabilidad</h2>
      <p>Prestamos el servicio con diligencia profesional y conforme a lo descrito en cada plan. No garantizamos que el software de terceros esté libre de errores ni que funcione sin interrupciones en cualquier entorno.</p>
      <p>En la medida permitida por la ley, la responsabilidad total de <?= e($legal) ?> frente al cliente, por cualquier concepto derivado del servicio, se limita al valor efectivamente pagado por el cliente en los doce meses anteriores al hecho que origina la reclamación. No respondemos por lucro cesante, pérdida de datos imputable a la infraestructura del cliente ni por daños indirectos. Nada en estos términos excluye las garantías legales irrenunciables reconocidas al consumidor por la legislación colombiana.</p>

      <h2 id="datos">12. Protección de datos personales</h2>
      <p>El tratamiento de los datos personales facilitados durante la contratación se rige por nuestra <a class="link" href="<?= e(url('privacidad.php')) ?>">política de privacidad</a>, conforme a la Ley 1581 de 2012 y sus normas reglamentarias. El servicio opera dentro de la red local del cliente y no transfiere a <?= e($legal) ?> las pantallas, archivos ni la actividad de los equipos supervisados.</p>

      <h2 id="reembolsos">13. Reembolsos y cancelación</h2>
      <p>Las condiciones de retracto, reembolso y cancelación se detallan en la <a class="link" href="<?= e(url('reembolsos.php')) ?>">política de reembolsos</a>, que forma parte integral de estos términos.</p>

      <h2 id="terminacion">14. Terminación</h2>
      <p>El cliente puede dejar de usar el servicio en cualquier momento; los efectos económicos se rigen por la política de reembolsos. <?= e($legal) ?> puede terminar el servicio por incumplimiento grave de estos términos, por uso ilícito o por falta de pago, previa comunicación y, cuando proceda, devolución de la parte no ejecutada.</p>

      <h2 id="ley">15. Modificaciones, ley aplicable y controversias</h2>
      <p>Podemos actualizar estos términos para reflejar cambios en el servicio o en la normativa. La versión vigente es siempre la publicada en esta página, con su fecha de actualización. Los cambios no afectan las condiciones económicas de las licencias ya pagadas durante su vigencia.</p>
      <p>Estos términos se rigen por las leyes de la República de Colombia. Las controversias se someterán a los jueces competentes del domicilio del prestador, sin perjuicio de los derechos que la ley reconozca al cliente como consumidor y de la competencia de la Superintendencia de Industria y Comercio.</p>

      <h2 id="contacto">16. Contacto</h2>
      <p><b><?= e($legal) ?></b><br>Correo: <a class="link" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a><br>Sitio: <a class="link" href="<?= e(abs_url()) ?>">www.veyoncontrol.com</a></p>
    </article>

    <div class="legal-links">
      <a class="btn btn-ghost" href="<?= e(url('privacidad.php')) ?>">Política de privacidad</a>
      <a class="btn btn-ghost" href="<?= e(url('reembolsos.php')) ?>">Política de reembolsos</a>
      <a class="btn btn-ghost" href="<?= e(url('index.php#contacto')) ?>">Contacto</a>
    </div>
  </div>
</section>

<?php require APP_ROOT . '/app/views/public_footer.php'; ?>
