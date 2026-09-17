<?php
/**
 * Instalador / migrador. Solo por línea de comandos:
 *   php install/install.php
 * Es idempotente: crea la base de datos y las tablas si no existen y
 * carga los datos iniciales solo cuando faltan.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Solo CLI');
}

$config = require dirname(__DIR__) . '/config/config.php';
$db = $config['db'];

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $db['host'], $db['port']),
    $db['user'],
    $db['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `{$db['name']}`");

$sql = file_get_contents(__DIR__ . '/schema.sql');
foreach (array_filter(array_map('trim', preg_split('/;\s*[\r\n]+/', $sql))) as $stmt) {
    if (preg_match('/^(--.*\s*)*$/', $stmt)) {
        continue;
    }
    $pdo->exec($stmt);
}
echo "Tablas verificadas.\n";

// ---------- Ajustes ----------
$settings = [
    'company_name'       => 'Veyon Control',
    'company_tax_id'     => '',
    'company_address'    => '',
    'company_city'       => 'Bogotá, Colombia',
    'company_email'      => 'contacto@veyoncontrol.com',
    'company_phone'      => '',
    'tax_rate'           => '0',
    'tax_label'          => 'IVA',
    'eur_rate'           => '5000',
    'invoice_prefix'     => 'FV-',
    'quote_prefix'       => 'COT-',
    'invoice_due_days'   => '15',
    'quote_valid_days'   => '30',
    'license_months'     => '12',
    'invoice_footer'     => 'Comprobante electrónico de la transacción. Operación regida por las leyes de los Estados Unidos (estado de Delaware).',
    'hero_version'       => '4.11.2',
    'compare_labels'     => "Equipos cubiertos\nPaneles docentes\nActualizaciones de versión\nIntegración LDAP / Active Directory\nIntegración Microsoft Entra ID\nPaquetes de actualización (GPO / imagen)\nCapacitación del profesorado\nSoporte\nRevisión de actualizaciones",
    'notify_email'       => 'eortiz@colegioaleman.edu.co',
];
$ins = $pdo->prepare('INSERT IGNORE INTO settings (skey, svalue) VALUES (?, ?)');
foreach ($settings as $k => $v) {
    $ins->execute([$k, $v]);
}

// ---------- Contadores ----------
$pdo->exec("INSERT IGNORE INTO counters (name, value) VALUES ('invoice', 0), ('quote', 0)");

// ---------- Planes ----------
if ((int) $pdo->query('SELECT COUNT(*) FROM plans')->fetchColumn() === 0) {
    $plans = [
        ['aula', 'Licencia Aula', 'Hasta 10 equipos', 'Para un aula única o una sala de cómputo pequeña.', 3000000, 'por año · un aula',
            "Licencia para hasta 10 equipos\n1 panel docente (Veyon Master)\nActualizaciones de versión en todos los puestos\nVerificación de que cada equipo esté al día\nCapacitación inicial de 2 horas\nSoporte por correo durante 12 meses",
            "10\n1\nSí\n—\n—\n—\n2 h\nCorreo\n—", 0, null, 1],
        ['laboratorio', 'Licencia Laboratorio', 'Hasta 50 equipos', 'Para instituciones con varias salas o un laboratorio grande.', 8000000, 'por año · hasta 50 equipos',
            "Todo lo del plan Aula\nLicencia para hasta 50 equipos\nHasta 3 paneles docentes\nCompatibilidad con LDAP o Active Directory\nPaquetes de actualización listos para distribuir\nCapacitación de 4 horas para el equipo docente\nSoporte prioritario durante 12 meses",
            "50\n3\nSí\nSí\n—\nSí\n4 h\nPrioritario\n—", 1, 'Más solicitado', 2],
        ['campus', 'Licencia Campus', 'Hasta 100 equipos', 'Para sedes con varias aulas simultáneas y equipo de TI propio.', 10000000, 'por año · hasta 100 equipos',
            "Todo lo del plan Laboratorio\nLicencia para hasta 100 equipos\nPaneles docentes sin límite\nActualizaciones coordinadas por aula\nCapacitación por sedes y material para el profesorado\nRevisión semestral del estado de actualización",
            "100\nSin límite\nSí\nSí\nOpcional\nSí\nPor sedes\nPrioritario\nSemestral", 0, null, 3],
        ['sitio', 'Licencia de Sitio', 'Equipos ilimitados', 'Cobertura total de una sede, sin contar puestos uno a uno.', 22000000, 'por año · sede completa',
            "Todo lo del plan Campus\nEquipos ilimitados dentro de la sede\nPaquetes de actualización para imagen del sistema o GPO\nIntegración con Microsoft Entra ID\nCapacitación sin límite durante el año\nSoporte dedicado con tiempos de respuesta acordados\nAcompañamiento en auditorías y políticas de uso",
            "Ilimitados\nSin límite\nSí\nSí\nSí\nSí\nSin límite\nDedicado\nTrimestral", 0, null, 4],
    ];
    $st = $pdo->prepare('INSERT INTO plans (slug, name, capacity_label, subtitle, price_cop, period_label, features, compare_values, featured, ribbon, sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
    foreach ($plans as $p) {
        $st->execute($p);
    }
    echo "Planes cargados.\n";
}

// ---------- Precios de complementos ----------
if ((int) $pdo->query('SELECT COUNT(*) FROM addon_prices')->fetchColumn() === 0) {
    $st = $pdo->prepare('INSERT INTO addon_prices (org_type, single_price, bundle_price, includes, custom_quote, sort_order) VALUES (?,?,?,?,?,?)');
    $st->execute(['Institución educativa individual', 2500000, 4500000, 'Actualizaciones incluidas', 0, 1]);
    $st->execute(['Grupo de instituciones', 4000000, 7000000, 'Actualizaciones y capacitación', 0, 2]);
    $st->execute(['Universidad o centro superior', 6000000, 10000000, 'Actualizaciones, capacitación y soporte', 0, 3]);
    $st->execute(['Secretarías, distritos y empresas', null, null, 'Propuesta a medida', 1, 4]);
    echo "Precios de complementos cargados.\n";
}

// ---------- Administrador inicial ----------
$adminEmail = 'eortiz@colegioaleman.edu.co';
$exists = $pdo->prepare('SELECT id FROM admins WHERE email = ?');
$exists->execute([$adminEmail]);
if (!$exists->fetchColumn()) {
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
    $password = '';
    for ($i = 0; $i < 14; $i++) {
        $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
    $st = $pdo->prepare('INSERT INTO admins (name, email, password_hash, must_change_password) VALUES (?,?,?,1)');
    $st->execute(['E. Ortiz', $adminEmail, password_hash($password, PASSWORD_DEFAULT)]);
    echo "\nAdministrador creado: {$adminEmail}\nContraseña temporal: {$password}\n(se pedirá cambiarla en el primer ingreso)\n";
} else {
    echo "Administrador {$adminEmail} ya existe.\n";
}

echo "\nInstalación completada.\n";
