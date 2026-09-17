<?php
/**
 * Configuración base. No edites este archivo para poner secretos:
 * sobreescribe cualquier clave en config/config.local.php
 */
$config = [
    'app_name' => 'Veyon Control',
    // URL pública completa (https://midominio.com). Vacío = autodetectar.
    // Es obligatoria en producción para que Mercado Pago pueda enviar webhooks.
    'app_url'  => '',
    'debug'    => false,
    'timezone' => 'America/Bogota',

    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'veyoncontrol',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    'mercadopago' => [
        'public_key'           => '',
        'access_token'         => '',
        // Clave secreta de firma de webhooks (Tus integraciones > Webhooks)
        'webhook_secret'       => '',
        'statement_descriptor' => 'VEYONCONTROL',
    ],

    'mail' => [
        // Remitente usado por mail(); en XAMPP normalmente no hay SMTP configurado
        'from' => 'no-reply@veyoncontrol.local',
    ],
];

$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    $config = array_replace_recursive($config, require $local);
}
return $config;
