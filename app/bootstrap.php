<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

$GLOBALS['config'] = require APP_ROOT . '/config/config.php';

date_default_timezone_set(config('timezone'));
mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', config('debug') ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', APP_ROOT . '/storage/logs/php-error.log');

require APP_ROOT . '/app/helpers.php';
require APP_ROOT . '/app/db.php';
require APP_ROOT . '/app/auth.php';
require APP_ROOT . '/app/billing.php';
require APP_ROOT . '/app/mercadopago.php';

function config(string $key, $default = null)
{
    $value = $GLOBALS['config'];
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

// ---------- Sesión segura ----------
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_name('vcsess');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => base_path() . '/',
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', '7200');
    session_start();

    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: SAMEORIGIN');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

// ---------- Errores no controlados ----------
set_exception_handler(function (Throwable $e) {
    error_log((string) $e);
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, (string) $e . PHP_EOL);
        exit(1);
    }
    if (!headers_sent()) {
        http_response_code(500);
    }
    if (config('debug')) {
        echo '<pre style="white-space:pre-wrap;padding:1rem;font:13px monospace">' . e((string) $e) . '</pre>';
    } else {
        echo '<!doctype html><meta charset="utf-8"><title>Error</title><div style="font-family:system-ui;max-width:520px;margin:15vh auto;text-align:center"><h1>Algo salió mal</h1><p>Ocurrió un error inesperado. Intenta de nuevo en unos minutos.</p></div>';
    }
    exit;
});
