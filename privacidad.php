<?php
require __DIR__ . '/app/bootstrap.php';

$page = [
    'title'       => 'Política de privacidad y tratamiento de datos — Veyon Control',
    'description' => 'Cómo Grupo Logic SAS Latinoamérica recolecta, usa y protege los datos personales de los clientes de Veyon Control, conforme a la Ley 1581 de 2012.',
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
      <h1>Política de <span class="grad-text">privacidad</span></h1>
      <p class="lead mt-2">Cómo <?= e($legal) ?> recolecta, usa, conserva y protege los datos personales de quienes contratan o usan los servicios de Veyon Control.</p>
      <p class="small muted mt-2">Última actualización: <?= e(fdate($updated)) ?></p>
    </div>
  </div>
</section>

<section style="padding-top:0">
  <div class="container legal">
    <nav class="legal-nav" aria-label="Contenido">
      <a href="#responsable">1. Responsable</a>
      <a href="#datos">2. Datos que tratamos</a>
      <a href="#finalidades">3. Finalidades</a>
      <a href="#autorizacion">4. Autorización</a>
      <a href="#pagos">5. Datos de pago</a>
      <a href="#aula">6. Datos del aula</a>
      <a href="#cookies">7. Cookies</a>
      <a href="#terceros">8. Terceros</a>
      <a href="#conservacion">9. Conservación</a>
      <a href="#derechos">10. Tus derechos</a>
      <a href="#seguridad">11. Seguridad</a>
      <a href="#cambios">12. Cambios</a>
    </nav>

    <article class="legal-body">
      <h2 id="responsable">1. Responsable del tratamiento</h2>
      <p><b><?= e($legal) ?></b><?= setting('company_tax_id') ? ', NIT ' . e(setting('company_tax_id')) : '' ?>, sociedad colombiana que opera bajo la marca Veyon Control, es responsable del tratamiento de los datos personales recolectados a través de <a class="link" href="<?= e(abs_url()) ?>">www.veyoncontrol.com</a>.</p>
      <p>Canal único de atención para privacidad: <a class="link" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a>.</p>

      <h2 id="datos">2. Datos que tratamos</h2>
      <ul>
        <li><b>Datos de contacto institucional:</b> nombre de la institución, NIT, nombre y cargo de la persona de contacto, correo electrónico, teléfono (si decides facilitarlo), ciudad y dirección.</li>
        <li><b>Datos de cuenta:</b> correo de acceso y contraseña almacenada siempre cifrada mediante funciones de derivación de un solo sentido. Nunca conservamos tu contraseña en texto legible.</li>
        <li><b>Datos de la relación comercial:</b> cotizaciones, facturas, pagos, licencias contratadas y comunicaciones de soporte.</li>
        <li><b>Datos técnicos:</b> dirección IP, fecha y hora de acceso y registros de actividad del panel, necesarios para la seguridad y la trazabilidad de las operaciones.</li>
      </ul>
      <p>Solo pedimos los datos necesarios para prestar el servicio. Los campos opcionales, como el teléfono, pueden dejarse en blanco.</p>

      <h2 id="finalidades">3. Finalidades del tratamiento</h2>
      <ul>
        <li>Atender solicitudes de cotización y preparar propuestas comerciales.</li>
        <li>Crear y administrar la cuenta del cliente en el portal.</li>
        <li>Gestionar la contratación: facturación, cobro, comprobantes y control de vigencia de las licencias.</li>
        <li>Prestar soporte técnico y capacitación.</li>
        <li>Avisar sobre vencimientos, renovaciones y cambios relevantes del servicio.</li>
        <li>Prevenir fraude y abuso, y cumplir obligaciones legales, contables y tributarias.</li>
      </ul>
      <p>No vendemos ni cedemos datos personales a terceros con fines publicitarios, ni realizamos decisiones automatizadas con efectos jurídicos sobre los titulares.</p>

      <h2 id="autorizacion">4. Autorización y base legal</h2>
      <p>Tratamos los datos con la autorización que otorgas al registrarte, solicitar una cotización o contratar, y en ejecución del contrato que nos vincula, conforme a la Ley 1581 de 2012, el Decreto 1074 de 2015 y demás normas que las desarrollen. Determinados tratamientos se fundan además en el cumplimiento de obligaciones legales, como la conservación de soportes contables.</p>

      <h2 id="pagos">5. Datos de pago</h2>
      <p>Los pagos en línea se procesan por proveedores de pago autorizados. <b>No recibimos, no vemos ni almacenamos números de tarjeta, claves ni credenciales bancarias.</b> De cada operación conservamos únicamente el identificador de la transacción, el medio de pago empleado, el estado, el valor y la fecha, que son los datos necesarios para conciliar el pago con tu factura.</p>

      <h2 id="aula">6. Datos del aula y de los estudiantes</h2>
      <p>Veyon funciona dentro de la red local de tu institución: las pantallas, los archivos y la actividad de los equipos supervisados <b>no se transmiten a nuestros servidores</b> ni a servicios externos. No tenemos acceso a esa información y no tratamos datos de estudiantes.</p>
      <p>La institución es responsable de informar a su comunidad educativa sobre el uso de herramientas de supervisión en el aula y de cumplir la normativa que le resulte aplicable.</p>

      <h2 id="cookies">7. Cookies y almacenamiento local</h2>
      <p>El sitio usa únicamente lo imprescindible para funcionar:</p>
      <ul>
        <li><b>Cookie de sesión</b> (<span class="mono">vcsess</span>): mantiene tu sesión iniciada en el portal o en el panel. Se elimina al cerrar sesión o al expirar.</li>
        <li><b>Almacenamiento local del navegador:</b> recuerda la moneda que eliges para ver los precios.</li>
      </ul>
      <p>No usamos cookies de publicidad, de redes sociales ni de analítica de terceros. El video tutorial se carga solo cuando haces clic en él, y en ese momento se establece conexión con YouTube en su modo sin cookies de seguimiento.</p>

      <h2 id="terceros">8. Encargados y terceros</h2>
      <p>Para operar el servicio nos apoyamos en proveedores que actúan como encargados del tratamiento, cada uno con sus propias garantías de seguridad:</p>
      <ul>
        <li><b>Proveedor de pagos:</b> procesa las transacciones y la información financiera de la operación.</li>
        <li><b>Proveedor de infraestructura:</b> alojamiento del sitio y de la base de datos.</li>
        <li><b>Proveedor de correo:</b> envío de notificaciones transaccionales.</li>
        <li><b>Google Fonts y YouTube:</b> tipografías del sitio y reproducción del video, cuando el navegador los solicita.</li>
      </ul>
      <p>Algunos de estos proveedores pueden tratar datos fuera de Colombia. En esos casos exigimos niveles adecuados de protección mediante los contratos y cláusulas correspondientes.</p>

      <h2 id="conservacion">9. Conservación</h2>
      <p>Conservamos los datos mientras exista la relación comercial y, después, durante los plazos exigidos por la normativa contable y tributaria colombiana (en general, diez años para los soportes de las operaciones). Los registros técnicos de seguridad se conservan por periodos cortos. Cumplidos los plazos, los datos se eliminan o se anonimizan.</p>

      <h2 id="derechos">10. Derechos de los titulares</h2>
      <p>Como titular de los datos puedes conocer, actualizar, rectificar y suprimir tus datos; solicitar prueba de la autorización; ser informado sobre el uso dado a tus datos; revocar la autorización cuando no exista un deber legal o contractual que lo impida; y presentar quejas ante la Superintendencia de Industria y Comercio.</p>
      <p>Para ejercerlos, escribe a <a class="link" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a> indicando tu solicitud y los datos que permitan identificarte. Respondemos las consultas en un máximo de diez días hábiles y los reclamos en un máximo de quince días hábiles, prorrogables conforme a la ley. Desde tu portal puedes además consultar y actualizar directamente los datos de tu institución.</p>

      <h2 id="seguridad">11. Medidas de seguridad</h2>
      <ul>
        <li>Cifrado del sitio con HTTPS en todas las páginas.</li>
        <li>Contraseñas almacenadas con funciones de hash robustas y bloqueo temporal tras intentos fallidos.</li>
        <li>Separación de accesos entre el portal del cliente y la administración, con aislamiento de la información de cada cliente.</li>
        <li>Protección frente a falsificación de peticiones y a inyección de código en todas las consultas.</li>
        <li>Registro de auditoría de las operaciones administrativas y respaldos periódicos.</li>
      </ul>

      <h2 id="cambios">12. Cambios en esta política</h2>
      <p>Podemos actualizar esta política para reflejar cambios en el servicio o en la normativa. La versión vigente es la publicada en esta página, con su fecha de actualización. Si el cambio es sustancial, lo informamos por correo a los clientes activos.</p>

      <h2 id="contacto">13. Contacto</h2>
      <p><b><?= e($legal) ?></b><br>Correo: <a class="link" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a><br>Sitio: <a class="link" href="<?= e(abs_url()) ?>">www.veyoncontrol.com</a></p>
    </article>

    <div class="legal-links">
      <a class="btn btn-ghost" href="<?= e(url('terminos.php')) ?>">Términos y condiciones</a>
      <a class="btn btn-ghost" href="<?= e(url('reembolsos.php')) ?>">Política de reembolsos</a>
      <a class="btn btn-ghost" href="<?= e(url('index.php#contacto')) ?>">Contacto</a>
    </div>
  </div>
</section>

<?php require APP_ROOT . '/app/views/public_footer.php'; ?>
