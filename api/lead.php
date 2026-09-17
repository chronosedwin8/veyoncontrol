<?php
/** Recibe el formulario de cotización del sitio público y lo guarda como solicitud. */
require dirname(__DIR__) . '/app/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

function lead_fail(string $message, int $code = 422): void
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!is_post()) {
    lead_fail('Método no permitido', 405);
}
if (!hash_equals(csrf_token(), (string) ($_POST['_csrf'] ?? ''))) {
    lead_fail('La sesión expiró. Recarga la página e inténtalo de nuevo.', 403);
}
// Trampa para bots: campo oculto que una persona nunca llena
if (!empty($_POST['website'])) {
    echo json_encode(['ok' => true, 'message' => 'Solicitud recibida.']);
    exit;
}
$recent = (int) db_val('SELECT COUNT(*) FROM leads WHERE ip = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)', [client_ip()]);
if ($recent >= 5) {
    lead_fail('Has enviado varias solicitudes seguidas. Inténtalo más tarde.', 429);
}

$data = [
    'institution'  => mb_substr(input('institucion'), 0, 190),
    'contact_name' => mb_substr(input('nombre'), 0, 150),
    'position'     => mb_substr(input('cargo'), 0, 120),
    'email'        => mb_strtolower(mb_substr(input('correo'), 0, 190)),
    'phone'        => mb_substr(input('telefono'), 0, 40),
    'equipment'    => mb_substr(input('equipos'), 0, 80),
    'os'           => mb_substr(input('so'), 0, 40),
    'message'      => mb_substr(input('mensaje'), 0, 5000),
    'ip'           => client_ip(),
];

if ($data['institution'] === '' || $data['contact_name'] === '' || $data['equipment'] === '') {
    lead_fail('Completa los campos obligatorios.');
}
if (!valid_email($data['email'])) {
    lead_fail('El correo no es válido.');
}

$data['client_id'] = db_val('SELECT id FROM clients WHERE email = ?', [$data['email']]) ?: null;
$id = db_insert('leads', $data);
log_activity('lead_created', 'lead', $id, $data['institution']);

$notify = setting('notify_email');
if ($notify !== '') {
    send_mail(
        $notify,
        'Nueva solicitud de cotización - ' . $data['institution'],
        "Institución: {$data['institution']}\nContacto: {$data['contact_name']} ({$data['position']})\nCorreo: {$data['email']}\nTeléfono: {$data['phone']}\nEquipos: {$data['equipment']}\nSistema: {$data['os']}\n\n{$data['message']}\n\nVer en el panel: " . abs_url('admin/solicitudes.php')
    );
}

echo json_encode(['ok' => true, 'message' => '¡Gracias! Recibimos tu solicitud y te responderemos en menos de 48 horas hábiles.'], JSON_UNESCAPED_UNICODE);
