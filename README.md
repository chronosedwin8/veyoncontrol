# Veyon Control

Sitio web comercial de **Veyon Control**: licenciamiento, actualizaciones permanentes y soporte profesional de Veyon para aulas, laboratorios y salas de formación. Incluye **portal de clientes**, **panel de administración** y **pagos en línea con Mercado Pago**.

Construido en **PHP 8.1+ y MySQL 8**, sin frameworks ni Composer. Producción: https://www.veyoncontrol.com

## Estructura

```
veyoncontrol/
├── index.php, descargas.php, complementos.php, acerca.php, participa.php   Sitio público (precios desde la BD)
├── checkout.php        Compra en línea de un plan → factura → Mercado Pago
├── portal/             Portal de clientes: resumen, licencias, facturas, cotizaciones, pagos, perfil
├── admin/              Administración: clientes, solicitudes, cotizaciones, facturas, pagos,
│                       licencias, precios del home, configuración, administradores, actividad
├── api/lead.php        Formulario de cotización del sitio (guarda solicitudes)
├── api/mp_webhook.php  Webhook de Mercado Pago (firma verificada)
├── app/                Núcleo: BD, autenticación, facturación, Mercado Pago, vistas   (bloqueado a la web)
├── config/             Configuración; secretos en config.local.php                    (bloqueado a la web)
├── install/            Esquema SQL e instalador                                        (bloqueado a la web)
├── css/, js/           Estilos y JS del sitio público (tema claro)
└── assets/             Imágenes, estilos y JS del panel y el portal
```

## Instalación

1. Requisitos: PHP 8.1+ (pdo_mysql, curl, mbstring, openssl), MySQL 8, Apache con `mod_rewrite` y `AllowOverride All`.
2. Copia `config/config.local.example.php` como `config/config.local.php` y completa la base de datos y Mercado Pago.
3. Ejecuta el instalador (idempotente; crea la base, las tablas, los planes y el administrador inicial con contraseña temporal):
   ```
   php install/install.php
   ```
4. Ingresa en `/admin/` con la contraseña temporal que muestra el instalador; se pedirá cambiarla.

## Mercado Pago

1. En `config/config.local.php` completa `public_key`, `access_token` y `webhook_secret`.
2. En producción `app_url` debe ser la URL HTTPS pública (la plantilla la define para `www.veyoncontrol.com`).
3. En Mercado Pago → Tus integraciones → Webhooks registra `https://www.veyoncontrol.com/api/mp_webhook.php` con el evento **Pagos**.
4. En el panel: Configuración → «Probar conexión».

En local (sin HTTPS) Mercado Pago no puede enviar webhooks: el pago se confirma al volver del checkout
(`portal/pago_resultado.php`) y con «Sincronizar pagos con Mercado Pago» en cada factura.

### Flujo de pagos
Factura (`external_reference = INV-{id}`) → preferencia Checkout Pro → pago → webhook/retorno →
consulta del pago en la API → registro idempotente por `mp_payment_id` → factura parcial/pagada →
las líneas con plan activan la licencia anual (encadenada si es renovación).

## Seguridad
- Contraseñas con `password_hash`, bloqueo tras intentos fallidos, sesiones `HttpOnly`/`SameSite`.
- Tokens CSRF en todos los formularios y consultas preparadas con PDO.
- Aislamiento de datos por cliente en el portal y registro de auditoría en el panel.
- Firma HMAC de los webhooks y consulta del pago a la API (no se confía en el contenido de la notificación).
- `config/config.local.php` nunca se versiona.
