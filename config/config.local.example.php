<?php
/**
 * Plantilla de configuración local. Cópiala como config/config.local.php
 * y completa los valores. config.local.php NO se versiona.
 */
$host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$isProduction = in_array($host, ['www.veyoncontrol.com', 'veyoncontrol.com'], true);

return [
    'app_url' => $isProduction ? 'https://www.veyoncontrol.com' : '',
    'debug'   => !$isProduction,

    'db' => [
        'host' => '127.0.0.1',
        'name' => 'veyoncontrol',
        'user' => 'root',
        'pass' => '',
    ],

    // Mercado Pago > Tus integraciones > Credenciales / Webhooks
    'mercadopago' => [
        'public_key'     => '',
        'access_token'   => '',
        'webhook_secret' => '',
    ],
];
