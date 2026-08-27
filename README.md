# Veyon Control

Sitio web comercial de **Veyon Control**: licenciamiento, actualizaciones permanentes y soporte profesional de Veyon para aulas, laboratorios y salas de formación.

Construido en **HTML5, CSS3 y JavaScript puro**, sin frameworks ni dependencias de compilación. La única carga externa son las fuentes de Google Fonts.

## Estructura

```
veyoncontrol/
├── index.html          Home: hero, funciones, planes, capturas, video, proceso, FAQ, contacto
├── descargas.html      Instaladores para Windows y Linux, PPA, versiones anteriores
├── complementos.html   Catálogo de complementos, paquetes y precios
├── participa.html      Soporte y recursos para clientes con plan activo
├── acerca.html         Arquitectura, seguridad, condiciones de uso
├── css/style.css       Hoja de estilos completa (tema oscuro, glassmorphism)
├── js/app.js           16 módulos en JS puro, sin dependencias
└── assets/img/         Logotipo propio (SVG) y capturas de la aplicación
```

## Funcionalidad del front-end

| Módulo | Descripción |
|---|---|
| Navegación | Menú fijo, submenú "Recursos", menú móvil a pantalla completa |
| Selector de moneda | COP / EUR con tasa de referencia de 5.000 COP por euro, persistido en `localStorage` |
| Planes | Cuatro niveles de licencia anual con precios sincronizados al selector de moneda |
| Formulario | Solicitud de cotización con validación y envío por `mailto:` |
| Detección de SO | Recomienda automáticamente el instalador correcto en la página de descargas |
| Efectos | Red de nodos en canvas, aparición al hacer scroll, contadores, pestañas, parallax |
| Accesibilidad | Respeta `prefers-reduced-motion`, atributos ARIA en menús y pestañas |

## Configuración pendiente

- **Correo de contacto**: en `js/app.js`, la constante `CONTACT_EMAIL` apunta a `contacto@tudominio.com`. Reemplázala por la dirección real del área comercial.
- **Tasa de cambio**: definida en `js/app.js`, en el objeto `CURRENCIES`.

## Uso local

No requiere compilación. Abre `index.html` en el navegador o sirve la carpeta:

```bash
python -m http.server 8000
```

## Notas

Veyon es una aplicación desarrollada por el proyecto Veyon. Los enlaces de descarga de este sitio apuntan a los archivos publicados en su repositorio oficial de GitHub.
