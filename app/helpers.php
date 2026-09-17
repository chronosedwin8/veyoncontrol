<?php
declare(strict_types=1);

// ---------- Salida ----------
function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ---------- URLs ----------
function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
}

/** Ruta del proyecto relativa a la raíz del servidor web, p. ej. "/VeyonControl". */
function base_path(): string
{
    static $path = null;
    if ($path !== null) {
        return $path;
    }
    $appUrl = (string) config('app_url');
    if ($appUrl !== '') {
        return $path = rtrim((string) parse_url($appUrl, PHP_URL_PATH), '/');
    }
    $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
    $root = realpath(APP_ROOT) ?: APP_ROOT;
    $rel = '';
    if ($docRoot !== '' && stripos($root, $docRoot) === 0) {
        $rel = substr($root, strlen($docRoot));
    }
    return $path = rtrim(str_replace('\\', '/', $rel), '/');
}

/** URL absoluta de la aplicación (esquema + host + ruta base). */
function app_url(): string
{
    $appUrl = (string) config('app_url');
    if ($appUrl !== '') {
        return rtrim($appUrl, '/');
    }
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return (is_https() ? 'https' : 'http') . '://' . $host . base_path();
}

function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function abs_url(string $path = ''): string
{
    return app_url() . '/' . ltrim($path, '/');
}

/** Recurso estático con marca de versión para invalidar caché al cambiar. */
function asset(string $path): string
{
    $file = APP_ROOT . '/' . ltrim($path, '/');
    $v = is_file($file) ? filemtime($file) : 1;
    return url($path) . '?v=' . $v;
}

function redirect(string $path): void
{
    // URL completa o ruta absoluta (p. ej. la devuelta por safe_next) se usan tal cual
    $target = preg_match('#^(https?://|/(?!/))#', $path) ? $path : url($path);
    header('Location: ' . $target, true, 303);
    exit;
}

function is_local_url(string $url): bool
{
    $host = parse_url($url, PHP_URL_HOST) ?: '';
    return in_array($host, ['localhost', '127.0.0.1', '::1'], true)
        || preg_match('/^(10|192\.168|172\.(1[6-9]|2\d|3[01]))\./', $host)
        || str_ends_with($host, '.local') || str_ends_with($host, '.test');
}

/** Solo permite redirecciones internas (evita open redirect). */
function safe_next(?string $next, string $fallback): string
{
    $next = (string) $next;
    if ($next !== '' && str_starts_with($next, base_path() . '/') && !str_starts_with($next, '//')) {
        return $next;
    }
    return url($fallback);
}

// ---------- Peticiones ----------
function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function input(string $key, $default = '')
{
    $v = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($v) ? trim($v) : $v;
}

function input_int(string $key, int $default = 0): int
{
    $v = $_POST[$key] ?? $_GET[$key] ?? null;
    return is_numeric($v) ? (int) $v : $default;
}

/** Convierte "3.000.000", "3000000,50" o "3,000,000.50" a float. */
function parse_money($value): float
{
    $s = preg_replace('/[^\d,.\-]/', '', (string) $value);
    if ($s === '' || $s === '-') {
        return 0.0;
    }
    $lastComma = strrpos($s, ',');
    $lastDot = strrpos($s, '.');
    if ($lastComma !== false && $lastDot !== false) {
        $dec = $lastComma > $lastDot ? ',' : '.';
        $thousands = $dec === ',' ? '.' : ',';
        $s = str_replace($thousands, '', $s);
        $s = str_replace($dec, '.', $s);
    } elseif ($lastComma !== false) {
        // Solo comas: decimales si hay 1-2 dígitos al final, si no separador de miles
        $s = preg_match('/,\d{1,2}$/', $s) && substr_count($s, ',') === 1 ? str_replace(',', '.', $s) : str_replace(',', '', $s);
    } elseif ($lastDot !== false) {
        $s = preg_match('/\.\d{1,2}$/', $s) && substr_count($s, '.') === 1 ? $s : str_replace('.', '', $s);
    }
    return round((float) $s, 2);
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function valid_email(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL) && mb_strlen($email) <= 190;
}

function valid_date(string $date): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// ---------- Formato ----------
function money($amount, bool $withCode = false): string
{
    $amount = (float) $amount;
    $decimals = abs($amount - round($amount)) > 0.001 ? 2 : 0;
    $txt = '$ ' . number_format($amount, $decimals, ',', '.');
    return $withCode ? $txt . ' COP' : $txt;
}

function fdate($date, bool $time = false): string
{
    if (!$date) {
        return '—';
    }
    $ts = is_numeric($date) ? (int) $date : strtotime((string) $date);
    return $ts ? date($time ? 'd/m/Y H:i' : 'd/m/Y', $ts) : '—';
}

function num_input($amount): string
{
    $amount = (float) $amount;
    return abs($amount - round($amount)) > 0.001 ? number_format($amount, 2, '.', '') : (string) (int) round($amount);
}

function lines(?string $text): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $text)), 'strlen'));
}

// ---------- Mensajes flash ----------
function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function flashes(): array
{
    $f = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $f;
}

// ---------- CSRF ----------
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('La sesión expiró o la solicitud no es válida. Vuelve atrás, recarga la página e inténtalo de nuevo.');
    }
}

// ---------- Ajustes ----------
function settings(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db_all('SELECT skey, svalue FROM settings') as $row) {
            $cache[$row['skey']] = $row['svalue'];
        }
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $s = settings();
    return isset($s[$key]) && $s[$key] !== null ? (string) $s[$key] : $default;
}

function save_setting(string $key, string $value): void
{
    db_exec('INSERT INTO settings (skey, svalue) VALUES (?, ?) ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)', [$key, $value]);
}

// ---------- Bitácora ----------
function log_activity(string $action, ?string $entity = null, ?int $entityId = null, string $details = ''): void
{
    $actorType = 'system';
    $actorId = null;
    if (!empty($_SESSION['admin_id'])) {
        $actorType = 'admin';
        $actorId = (int) $_SESSION['admin_id'];
    } elseif (!empty($_SESSION['client_id'])) {
        $actorType = 'client';
        $actorId = (int) $_SESSION['client_id'];
    }
    try {
        db_exec(
            'INSERT INTO activity_log (actor_type, actor_id, action, entity, entity_id, details, ip) VALUES (?,?,?,?,?,?,?)',
            [$actorType, $actorId, $action, $entity, $entityId, mb_substr($details, 0, 500), PHP_SAPI === 'cli' ? null : client_ip()]
        );
    } catch (Throwable $e) {
        error_log('log_activity: ' . $e->getMessage());
    }
}

// ---------- Correo (mejor esfuerzo) ----------
function send_mail(string $to, string $subject, string $body): bool
{
    $headers = [
        'From: ' . setting('company_name', 'Veyon Control') . ' <' . config('mail.from') . '>',
        'Content-Type: text/plain; charset=UTF-8',
    ];
    $ok = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
    if (!$ok) {
        error_log("send_mail: no se pudo enviar a {$to} ({$subject})");
    }
    return $ok;
}

// ---------- Etiquetas de estado ----------
function invoice_status(array $inv): array
{
    $s = $inv['status'];
    if (in_array($s, ['pending', 'partial'], true) && !empty($inv['due_date']) && $inv['due_date'] < date('Y-m-d')) {
        return ['Vencida', 'danger'];
    }
    return [
        'draft'     => ['Borrador', 'muted'],
        'pending'   => ['Pendiente', 'warn'],
        'partial'   => ['Pago parcial', 'info'],
        'paid'      => ['Pagada', 'ok'],
        'cancelled' => ['Anulada', 'muted'],
    ][$s] ?? [$s, 'muted'];
}

function quote_status(array $q): array
{
    $s = $q['status'];
    if (in_array($s, ['draft', 'sent'], true) && !empty($q['valid_until']) && $q['valid_until'] < date('Y-m-d')) {
        return ['Vencida', 'danger'];
    }
    return [
        'draft'    => ['Borrador', 'muted'],
        'sent'     => ['Enviada', 'info'],
        'accepted' => ['Aceptada', 'ok'],
        'rejected' => ['Rechazada', 'danger'],
        'expired'  => ['Vencida', 'danger'],
        'invoiced' => ['Facturada', 'ok'],
    ][$s] ?? [$s, 'muted'];
}

function payment_status(string $s): array
{
    return [
        'approved'     => ['Aprobado', 'ok'],
        'pending'      => ['Pendiente', 'warn'],
        'in_process'   => ['En proceso', 'warn'],
        'rejected'     => ['Rechazado', 'danger'],
        'cancelled'    => ['Cancelado', 'muted'],
        'refunded'     => ['Reembolsado', 'muted'],
        'charged_back' => ['Contracargo', 'danger'],
    ][$s] ?? [$s, 'muted'];
}

function payment_methods(): array
{
    return [
        'mercadopago'   => 'Mercado Pago',
        'transferencia' => 'Transferencia bancaria',
        'consignacion'  => 'Consignación',
        'efectivo'      => 'Efectivo',
        'tarjeta'       => 'Tarjeta (datáfono)',
        'otro'          => 'Otro',
    ];
}

function lead_status(string $s): array
{
    return [
        'new'       => ['Nueva', 'warn'],
        'contacted' => ['Contactada', 'info'],
        'converted' => ['Convertida', 'ok'],
        'closed'    => ['Cerrada', 'muted'],
    ][$s] ?? [$s, 'muted'];
}

function badge(array $status): string
{
    return '<span class="pill pill-' . e($status[1]) . '">' . e($status[0]) . '</span>';
}

// ---------- Paginación ----------
function paginate(int $total, int $perPage = 25): array
{
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min(max(1, input_int('page', 1)), $pages);
    return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage, 'limit' => $perPage, 'total' => $total];
}

function pagination_links(array $p): string
{
    if ($p['pages'] <= 1) {
        return '';
    }
    $q = $_GET;
    $html = '<nav class="pager">';
    for ($i = 1; $i <= $p['pages']; $i++) {
        if ($p['pages'] > 12 && abs($i - $p['page']) > 3 && $i !== 1 && $i !== $p['pages']) {
            if (abs($i - $p['page']) === 4) {
                $html .= '<span>…</span>';
            }
            continue;
        }
        $q['page'] = $i;
        $html .= $i === $p['page']
            ? '<span class="on">' . $i . '</span>'
            : '<a href="?' . e(http_build_query($q)) . '">' . $i . '</a>';
    }
    return $html . '</nav>';
}

function random_password(int $length = 12): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
    $out = '';
    for ($i = 0; $i < $length; $i++) {
        $out .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
    return $out;
}
