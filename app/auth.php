<?php
declare(strict_types=1);

const LOGIN_MAX_PER_ACCOUNT = 5;   // intentos fallidos por cuenta en la ventana
const LOGIN_MAX_PER_IP      = 20;  // intentos fallidos por IP en la ventana
const LOGIN_WINDOW_MINUTES  = 15;

// ---------- Administradores ----------
function current_admin(): ?array
{
    static $admin = false;
    if ($admin === false) {
        $admin = empty($_SESSION['admin_id'])
            ? null
            : db_one('SELECT * FROM admins WHERE id = ? AND active = 1', [(int) $_SESSION['admin_id']]);
        if (!$admin) {
            unset($_SESSION['admin_id']);
        }
    }
    return $admin;
}

function require_admin(): array
{
    $admin = current_admin();
    if (!$admin) {
        redirect('admin/login.php?next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? ''));
    }
    $script = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if ($admin['must_change_password'] && !in_array($script, ['perfil.php', 'logout.php'], true)) {
        flash('warn', 'Por seguridad, define una contraseña nueva antes de continuar.');
        redirect('admin/perfil.php');
    }
    return $admin;
}

// ---------- Clientes ----------
function current_client(): ?array
{
    static $client = false;
    if ($client === false) {
        $client = empty($_SESSION['client_id'])
            ? null
            : db_one("SELECT * FROM clients WHERE id = ? AND status = 'active'", [(int) $_SESSION['client_id']]);
        if (!$client) {
            unset($_SESSION['client_id']);
        }
    }
    return $client;
}

function require_client(): array
{
    $client = current_client();
    if (!$client) {
        redirect('portal/login.php?next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? ''));
    }
    return $client;
}

// ---------- Inicio de sesión ----------
function login_throttled(string $scope, string $identifier): bool
{
    $since = date('Y-m-d H:i:s', time() - LOGIN_WINDOW_MINUTES * 60);
    $byAccount = (int) db_val(
        'SELECT COUNT(*) FROM login_attempts WHERE scope = ? AND identifier = ? AND success = 0 AND created_at > ?',
        [$scope, $identifier, $since]
    );
    $byIp = (int) db_val(
        'SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND success = 0 AND created_at > ?',
        [client_ip(), $since]
    );
    return $byAccount >= LOGIN_MAX_PER_ACCOUNT || $byIp >= LOGIN_MAX_PER_IP;
}

function record_login_attempt(string $scope, string $identifier, bool $success): void
{
    db_exec(
        'INSERT INTO login_attempts (scope, identifier, ip, success) VALUES (?,?,?,?)',
        [$scope, $identifier, client_ip(), $success ? 1 : 0]
    );
    if ($success) {
        db_exec('DELETE FROM login_attempts WHERE scope = ? AND identifier = ? AND success = 0', [$scope, $identifier]);
    }
    // Limpieza ocasional
    if (random_int(1, 50) === 1) {
        db_exec('DELETE FROM login_attempts WHERE created_at < ?', [date('Y-m-d H:i:s', time() - 86400)]);
    }
}

/**
 * Intenta autenticar. Devuelve [fila|null, mensajeError|null].
 */
function attempt_login(string $scope, string $email, string $password): array
{
    $email = mb_strtolower(trim($email));
    if (login_throttled($scope, $email)) {
        return [null, 'Demasiados intentos fallidos. Espera ' . LOGIN_WINDOW_MINUTES . ' minutos antes de volver a intentarlo.'];
    }
    $row = $scope === 'admin'
        ? db_one('SELECT * FROM admins WHERE email = ? AND active = 1', [$email])
        : db_one("SELECT * FROM clients WHERE email = ? AND status = 'active'", [$email]);

    // Verificación en tiempo constante aunque el usuario no exista
    $hash = $row['password_hash'] ?? '$2y$10$496Ie0FU0Z53ve.SwkdaFuJXqB9qrhnJmTMMsK1gC19/vlNinQhVW';
    $ok = password_verify($password, (string) $hash) && $row && !empty($row['password_hash']);

    record_login_attempt($scope, $email, $ok);
    if (!$ok) {
        return [null, 'Correo o contraseña incorrectos.'];
    }

    if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) {
        $table = $scope === 'admin' ? 'admins' : 'clients';
        db_update($table, ['password_hash' => password_hash($password, PASSWORD_DEFAULT)], 'id = ?', [$row['id']]);
    }
    return [$row, null];
}

function start_user_session(string $scope, int $id): void
{
    session_regenerate_id(true);
    $_SESSION[$scope === 'admin' ? 'admin_id' : 'client_id'] = $id;
    $table = $scope === 'admin' ? 'admins' : 'clients';
    db_update($table, ['last_login_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
}

function end_user_session(string $scope): void
{
    unset($_SESSION[$scope === 'admin' ? 'admin_id' : 'client_id']);
    session_regenerate_id(true);
}

function password_problem(string $password, string $confirm): ?string
{
    if (mb_strlen($password) < 8) {
        return 'La contraseña debe tener al menos 8 caracteres.';
    }
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        return 'La contraseña debe combinar letras y números.';
    }
    if ($password !== $confirm) {
        return 'Las contraseñas no coinciden.';
    }
    return null;
}

// ---------- Restablecimiento de contraseña ----------
function create_password_reset(string $userType, int $userId, int $hours = 48): string
{
    $token = bin2hex(random_bytes(32));
    db_exec('UPDATE password_resets SET used_at = NOW() WHERE user_type = ? AND user_id = ? AND used_at IS NULL', [$userType, $userId]);
    db_insert('password_resets', [
        'user_type'  => $userType,
        'user_id'    => $userId,
        'token_hash' => hash('sha256', $token),
        'expires_at' => date('Y-m-d H:i:s', time() + $hours * 3600),
    ]);
    return $token;
}

function find_password_reset(string $token): ?array
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        return null;
    }
    return db_one(
        'SELECT * FROM password_resets WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()',
        [hash('sha256', $token)]
    );
}
