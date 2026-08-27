/* ============================================================
   Veyon Control - JavaScript puro (sin dependencias)
   ============================================================ */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------------------------------------------------
     1. Navegacion: menu movil, sombra al hacer scroll
  --------------------------------------------------------- */
  function initNav() {
    var nav = document.querySelector('.nav');
    var burger = document.querySelector('.burger');
    var links = document.querySelector('.nav-links');

    if (burger && links) {
      burger.addEventListener('click', function () {
        var open = document.body.classList.toggle('nav-open');
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
      links.addEventListener('click', function (e) {
        if (e.target.tagName === 'A') {
          document.body.classList.remove('nav-open');
          burger.setAttribute('aria-expanded', 'false');
        }
      });
      window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') document.body.classList.remove('nav-open');
      });
    }

    if (nav) {
      var onScroll = function () {
        nav.classList.toggle('scrolled', window.scrollY > 20);
      };
      onScroll();
      window.addEventListener('scroll', onScroll, { passive: true });
    }
  }

  /* ---------------------------------------------------------
     2. Barra de progreso de lectura + boton volver arriba
  --------------------------------------------------------- */
  function initProgress() {
    var bar = document.querySelector('.scroll-progress');
    var top = document.querySelector('.to-top');
    if (!bar && !top) return;

    var tick = function () {
      var h = document.documentElement.scrollHeight - window.innerHeight;
      var p = h > 0 ? (window.scrollY / h) * 100 : 0;
      if (bar) bar.style.width = p + '%';
      if (top) top.classList.toggle('show', window.scrollY > 600);
    };
    tick();
    window.addEventListener('scroll', tick, { passive: true });

    if (top) {
      top.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
      });
    }
  }

  /* ---------------------------------------------------------
     3. Aparicion progresiva al hacer scroll
  --------------------------------------------------------- */
  function initReveal() {
    var items = document.querySelectorAll('.reveal');
    if (!items.length) return;

    if (!('IntersectionObserver' in window) || reduceMotion) {
      items.forEach(function (el) { el.classList.add('in'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
    items.forEach(function (el) { io.observe(el); });
  }

  /* ---------------------------------------------------------
     4. Brillo que sigue al cursor en las tarjetas
  --------------------------------------------------------- */
  function initCardGlow() {
    if (reduceMotion) return;
    document.addEventListener('pointermove', function (e) {
      var card = e.target.closest ? e.target.closest('.card') : null;
      if (!card) return;
      var r = card.getBoundingClientRect();
      card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      card.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }, { passive: true });
  }

  /* ---------------------------------------------------------
     5. Contadores animados
  --------------------------------------------------------- */
  function initCounters() {
    var nums = document.querySelectorAll('[data-count]');
    if (!nums.length) return;

    var run = function (el) {
      var target = parseFloat(el.getAttribute('data-count'));
      var suffix = el.getAttribute('data-suffix') || '';
      var decimals = (el.getAttribute('data-decimals') | 0);
      var dur = 1500;
      var t0 = performance.now();

      var frame = function (now) {
        var p = Math.min((now - t0) / dur, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = (target * eased).toFixed(decimals) + suffix;
        if (p < 1) requestAnimationFrame(frame);
      };
      requestAnimationFrame(frame);
    };

    if (!('IntersectionObserver' in window) || reduceMotion) {
      nums.forEach(function (el) {
        el.textContent = el.getAttribute('data-count') + (el.getAttribute('data-suffix') || '');
      });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { run(entry.target); io.unobserve(entry.target); }
      });
    }, { threshold: 0.5 });
    nums.forEach(function (el) { io.observe(el); });
  }

  /* ---------------------------------------------------------
     6. Pestanas de la galeria de capturas
  --------------------------------------------------------- */
  function initTabs() {
    var groups = document.querySelectorAll('[data-tabs]');
    groups.forEach(function (group) {
      var tabs = group.querySelectorAll('.tab');
      var panels = group.querySelectorAll('.tab-panel');
      tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
          var id = tab.getAttribute('data-target');
          tabs.forEach(function (t) {
            var on = t === tab;
            t.classList.toggle('active', on);
            t.setAttribute('aria-selected', on ? 'true' : 'false');
          });
          panels.forEach(function (p) {
            p.classList.toggle('active', p.id === id);
          });
        });
      });
    });
  }

  /* ---------------------------------------------------------
     7. Efecto de escritura en el titular
  --------------------------------------------------------- */
  function initTyping() {
    var el = document.querySelector('[data-typing]');
    if (!el) return;
    var words;
    try { words = JSON.parse(el.getAttribute('data-typing')); } catch (err) { return; }
    if (!words || !words.length) return;

    if (reduceMotion) { el.textContent = words[0]; return; }

    var w = 0, c = 0, deleting = false;
    var loop = function () {
      var word = words[w];
      c += deleting ? -1 : 1;
      el.textContent = word.slice(0, c);
      var delay = deleting ? 45 : 85;
      if (!deleting && c === word.length) { deleting = true; delay = 1800; }
      else if (deleting && c === 0) { deleting = false; w = (w + 1) % words.length; delay = 320; }
      setTimeout(loop, delay);
    };
    loop();
  }

  /* ---------------------------------------------------------
     8. Fondo animado: red de nodos (canvas)
  --------------------------------------------------------- */
  function initCanvas() {
    var canvas = document.getElementById('fx-canvas');
    if (!canvas || reduceMotion) return;

    var ctx = canvas.getContext('2d');
    var dots = [];
    var w = 0, h = 0, dpr = Math.min(window.devicePixelRatio || 1, 2);
    var mouse = { x: -9999, y: -9999 };

    function resize() {
      w = canvas.clientWidth; h = canvas.clientHeight;
      canvas.width = w * dpr; canvas.height = h * dpr;
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      var count = Math.min(Math.round((w * h) / 22000), 90);
      dots = [];
      for (var i = 0; i < count; i++) {
        dots.push({
          x: Math.random() * w,
          y: Math.random() * h,
          vx: (Math.random() - 0.5) * 0.28,
          vy: (Math.random() - 0.5) * 0.28,
          r: Math.random() * 1.6 + 0.6
        });
      }
    }

    function draw() {
      ctx.clearRect(0, 0, w, h);
      for (var i = 0; i < dots.length; i++) {
        var d = dots[i];
        d.x += d.vx; d.y += d.vy;
        if (d.x < 0 || d.x > w) d.vx *= -1;
        if (d.y < 0 || d.y > h) d.vy *= -1;

        ctx.beginPath();
        ctx.arc(d.x, d.y, d.r, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(120,200,255,.55)';
        ctx.fill();

        for (var j = i + 1; j < dots.length; j++) {
          var o = dots[j];
          var dx = d.x - o.x, dy = d.y - o.y;
          var dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < 130) {
            ctx.beginPath();
            ctx.moveTo(d.x, d.y);
            ctx.lineTo(o.x, o.y);
            ctx.strokeStyle = 'rgba(90,150,255,' + (0.16 * (1 - dist / 130)) + ')';
            ctx.lineWidth = 1;
            ctx.stroke();
          }
        }

        var mdx = d.x - mouse.x, mdy = d.y - mouse.y;
        var md = Math.sqrt(mdx * mdx + mdy * mdy);
        if (md < 180) {
          ctx.beginPath();
          ctx.moveTo(d.x, d.y);
          ctx.lineTo(mouse.x, mouse.y);
          ctx.strokeStyle = 'rgba(34,211,238,' + (0.35 * (1 - md / 180)) + ')';
          ctx.lineWidth = 1;
          ctx.stroke();
        }
      }
      requestAnimationFrame(draw);
    }

    window.addEventListener('resize', resize);
    window.addEventListener('pointermove', function (e) {
      mouse.x = e.clientX; mouse.y = e.clientY;
    }, { passive: true });
    window.addEventListener('pointerleave', function () { mouse.x = mouse.y = -9999; });

    resize();
    requestAnimationFrame(draw);
  }

  /* ---------------------------------------------------------
     9. Deteccion del sistema operativo (pagina de descargas)
  --------------------------------------------------------- */
  var RELEASE = '4.11.2';
  var BUILD = '4.11.2.0';
  var BASE = 'https://github.com/veyon/veyon/releases/download/v' + RELEASE + '/';

  function detectOS() {
    var ua = navigator.userAgent;
    var plat = (navigator.userAgentData && navigator.userAgentData.platform) || navigator.platform || '';
    var s = (ua + ' ' + plat).toLowerCase();

    if (/android/.test(s)) return 'android';
    if (/iphone|ipad|ipod/.test(s)) return 'ios';
    if (/win/.test(s)) {
      var is64 = /wow64|win64|x64|amd64/.test(s) || (navigator.userAgentData && navigator.userAgentData.platform === 'Windows');
      return is64 ? 'win64' : 'win32';
    }
    if (/ubuntu/.test(s)) return 'ubuntu';
    if (/fedora/.test(s)) return 'fedora';
    if (/debian/.test(s)) return 'debian';
    if (/linux/.test(s)) return 'linux';
    if (/mac/.test(s)) return 'mac';
    return 'otro';
  }

  function initOSDetect() {
    var box = document.getElementById('os-detect');
    if (!box) return;

    var nameEl = box.querySelector('[data-os-name]');
    var descEl = box.querySelector('[data-os-desc]');
    var btn = box.querySelector('[data-os-btn]');
    var os = detectOS();

    var map = {
      win64: {
        name: 'Windows (64 bits) detectado',
        desc: 'Instalador recomendado: ' + 'veyon-' + BUILD + '-win64-setup.exe - version ' + RELEASE,
        url: BASE + 'veyon-' + BUILD + '-win64-setup.exe',
        label: 'Descargar para Windows 64 bits'
      },
      win32: {
        name: 'Windows (32 bits) detectado',
        desc: 'Instalador recomendado: ' + 'veyon-' + BUILD + '-win32-setup.exe - version ' + RELEASE,
        url: BASE + 'veyon-' + BUILD + '-win32-setup.exe',
        label: 'Descargar para Windows 32 bits'
      },
      ubuntu: {
        name: 'Ubuntu detectado',
        desc: 'Paquete .deb para Ubuntu 24.04 LTS. Si usas otra version, revisa la tabla de Linux.',
        url: BASE + 'veyon_' + BUILD + '-ubuntu.24.04_amd64.deb',
        label: 'Descargar .deb para Ubuntu 24.04'
      },
      debian: {
        name: 'Debian detectado',
        desc: 'Paquete .deb para Debian 12. Si usas otra version, revisa la tabla de Linux.',
        url: BASE + 'veyon_' + BUILD + '-debian.12_amd64.deb',
        label: 'Descargar .deb para Debian 12'
      },
      fedora: {
        name: 'Fedora detectado',
        desc: 'Paquete .rpm para Fedora 43. Si usas otra version, revisa la tabla de Linux.',
        url: BASE + 'veyon-' + BUILD + '-fedora.43.x86_64.rpm',
        label: 'Descargar .rpm para Fedora 43'
      },
      linux: {
        name: 'Linux detectado',
        desc: 'Elige tu distribucion en la tabla de paquetes: Debian, Ubuntu, Fedora, openSUSE, RHEL o Rocky Linux.',
        url: '#linux',
        label: 'Ver paquetes para Linux'
      },
      mac: {
        name: 'macOS detectado',
        desc: 'No hay instalador para macOS. Escribenos y revisamos alternativas para tu aula.',
        url: 'index.html#contacto',
        label: 'Hablar con nosotros'
      },
      android: {
        name: 'Android detectado',
        desc: 'Veyon es software de escritorio. Descarga el instalador desde el equipo donde vayas a usarlo.',
        url: '#windows',
        label: 'Ver todos los paquetes'
      },
      ios: {
        name: 'iOS detectado',
        desc: 'Veyon es software de escritorio. Descarga el instalador desde el equipo donde vayas a usarlo.',
        url: '#windows',
        label: 'Ver todos los paquetes'
      },
      otro: {
        name: 'Sistema no identificado',
        desc: 'Selecciona manualmente el paquete que corresponda a tu sistema operativo.',
        url: '#windows',
        label: 'Ver todos los paquetes'
      }
    };

    var info = map[os] || map.otro;
    if (nameEl) nameEl.textContent = info.name;
    if (descEl) descEl.textContent = info.desc;
    if (btn) { btn.href = info.url; btn.querySelector('span').textContent = info.label; }
  }

  /* ---------------------------------------------------------
     10. Filtro de la tabla de paquetes Linux
  --------------------------------------------------------- */
  function initFilter() {
    var input = document.getElementById('pkg-filter');
    if (!input) return;
    var rows = document.querySelectorAll('[data-filterable] tbody tr');
    var empty = document.getElementById('pkg-empty');

    input.addEventListener('input', function () {
      var q = input.value.trim().toLowerCase();
      var visible = 0;
      rows.forEach(function (row) {
        var match = row.textContent.toLowerCase().indexOf(q) !== -1;
        row.style.display = match ? '' : 'none';
        if (match) visible++;
      });
      if (empty) empty.style.display = visible ? 'none' : 'block';
    });
  }

  /* ---------------------------------------------------------
     11. Copiar al portapapeles
  --------------------------------------------------------- */
  function initCopy() {
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var sel = btn.getAttribute('data-copy');
        var src = document.querySelector(sel);
        if (!src) return;
        var text = src.innerText.replace(/^\$\s?/gm, '').trim();
        var done = function () {
          var old = btn.textContent;
          btn.textContent = 'Copiado';
          setTimeout(function () { btn.textContent = old; }, 1600);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(done, function () {});
        } else {
          var ta = document.createElement('textarea');
          ta.value = text; document.body.appendChild(ta); ta.select();
          try { document.execCommand('copy'); done(); } catch (e) {}
          document.body.removeChild(ta);
        }
      });
    });
  }

  /* ---------------------------------------------------------
     12. Anio actual en el pie
  --------------------------------------------------------- */
  function initYear() {
    document.querySelectorAll('[data-year]').forEach(function (el) {
      el.textContent = new Date().getFullYear();
    });
  }

  /* ---------------------------------------------------------
     13. Parallax suave del bloque visual del hero
  --------------------------------------------------------- */
  function initParallax() {
    if (reduceMotion) return;
    var layers = document.querySelectorAll('[data-parallax]');
    if (!layers.length) return;
    window.addEventListener('scroll', function () {
      var y = window.scrollY;
      layers.forEach(function (el) {
        var speed = parseFloat(el.getAttribute('data-parallax')) || 0.05;
        el.style.transform = 'translate3d(0,' + (y * speed * -1) + 'px,0)';
      });
    }, { passive: true });
  }


  /* ---------------------------------------------------------
     14. Menu desplegable "Recursos"
  --------------------------------------------------------- */
  function initSubmenu() {
    var groups = document.querySelectorAll('.has-sub');
    if (!groups.length) return;

    groups.forEach(function (group) {
      var toggle = group.querySelector('.sub-toggle');
      if (!toggle) return;

      toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        var open = group.classList.toggle('open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        groups.forEach(function (g) {
          if (g !== group) {
            g.classList.remove('open');
            var t = g.querySelector('.sub-toggle');
            if (t) t.setAttribute('aria-expanded', 'false');
          }
        });
      });

      group.addEventListener('mouseenter', function () {
        if (window.innerWidth > 1180) group.classList.add('open');
      });
      group.addEventListener('mouseleave', function () {
        if (window.innerWidth > 1180) group.classList.remove('open');
      });
    });

    document.addEventListener('click', function () {
      groups.forEach(function (g) { g.classList.remove('open'); });
    });
    window.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') groups.forEach(function (g) { g.classList.remove('open'); });
    });
  }

  /* ---------------------------------------------------------
     15. Selector de moneda del sitio
     Base de precios: COP. Tasa de referencia: 5.000 COP = 1 EUR
  --------------------------------------------------------- */
  var CURRENCIES = {
    COP: { rate: 1,    sym: '$',  code: 'COP', decimals: 0, locale: 'es-CO', before: true },
    EUR: { rate: 5000, sym: '€', code: 'EUR', decimals: 0, locale: 'es-ES', before: false }
  };
  var CUR_KEY = 'vc-currency';

  function readCurrency() {
    try {
      var v = localStorage.getItem(CUR_KEY);
      if (v && CURRENCIES[v]) return v;
    } catch (e) {}
    return 'COP';
  }

  function formatMoney(cop, cur) {
    var c = CURRENCIES[cur] || CURRENCIES.COP;
    var value = cop / c.rate;
    var txt;
    try {
      txt = value.toLocaleString(c.locale, {
        minimumFractionDigits: c.decimals,
        maximumFractionDigits: c.decimals
      });
    } catch (e) {
      txt = String(Math.round(value));
    }
    return c.before ? (c.sym + ' ' + txt) : (txt + ' ' + c.sym);
  }

  function applyCurrency(cur) {
    var c = CURRENCIES[cur] ? cur : 'COP';

    document.querySelectorAll('[data-cop]').forEach(function (el) {
      var cop = parseFloat(el.getAttribute('data-cop'));
      if (isNaN(cop)) return;
      el.textContent = formatMoney(cop, c);
    });

    document.querySelectorAll('[data-cur-code]').forEach(function (el) {
      el.textContent = CURRENCIES[c].code;
    });

    document.querySelectorAll('.cur-switch button').forEach(function (btn) {
      var on = btn.getAttribute('data-cur') === c;
      btn.classList.toggle('on', on);
      btn.setAttribute('aria-pressed', on ? 'true' : 'false');
    });

    try { localStorage.setItem(CUR_KEY, c); } catch (e) {}
  }

  function initCurrency() {
    var current = readCurrency();
    applyCurrency(current);

    document.querySelectorAll('.cur-switch button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        applyCurrency(btn.getAttribute('data-cur'));
      });
    });
  }


  /* ---------------------------------------------------------
     16. Formulario de cotizacion
     >>> CAMBIA ESTE CORREO POR EL DE TU AREA COMERCIAL <<<
  --------------------------------------------------------- */
  var CONTACT_EMAIL = 'contacto@tudominio.com';

  function initForm() {
    var form = document.getElementById('form-cotizacion');
    if (!form) return;
    var note = document.getElementById('form-msg');

    var required = ['f-inst', 'f-nombre', 'f-mail', 'f-equipos'];

    function markInvalid(el, invalid) {
      var wrap = el.closest('.field');
      if (wrap) wrap.classList.toggle('invalid', invalid);
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var ok = true;
      var firstBad = null;

      required.forEach(function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        var bad = !el.value.trim();
        if (id === 'f-mail' && el.value.trim()) {
          bad = !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(el.value.trim());
        }
        markInvalid(el, bad);
        if (bad) { ok = false; if (!firstBad) firstBad = el; }
      });

      if (!ok) {
        if (note) { note.textContent = 'Revisa los campos marcados antes de enviar.'; note.classList.remove('ok'); }
        if (firstBad) firstBad.focus();
        return;
      }

      var get = function (id) {
        var el = document.getElementById(id);
        return el && el.value ? el.value.trim() : '(sin indicar)';
      };

      var asunto = 'Solicitud de cotizacion Veyon - ' + get('f-inst');
      var cuerpo = [
        'Institucion: ' + get('f-inst'),
        'Contacto: ' + get('f-nombre'),
        'Cargo: ' + get('f-cargo'),
        'Correo: ' + get('f-mail'),
        'Telefono: ' + get('f-tel'),
        'Equipos a cubrir: ' + get('f-equipos'),
        'Sistema operativo: ' + get('f-so'),
        '',
        'Mensaje:',
        get('f-msg')
      ].join('\n');

      window.location.href = 'mailto:' + CONTACT_EMAIL +
        '?subject=' + encodeURIComponent(asunto) +
        '&body=' + encodeURIComponent(cuerpo);

      if (note) {
        note.textContent = 'Abriendo tu gestor de correo con la solicitud lista para enviar.';
        note.classList.add('ok');
      }
    });

    form.addEventListener('input', function (e) {
      if (e.target.closest('.field')) markInvalid(e.target, false);
    });
  }

  /* ---------------------------------------------------------
     Arranque
  --------------------------------------------------------- */
  function boot() {
    initNav();
    initProgress();
    initReveal();
    initCardGlow();
    initCounters();
    initTabs();
    initTyping();
    initCanvas();
    initOSDetect();
    initFilter();
    initCopy();
    initYear();
    initParallax();
    initSubmenu();
    initCurrency();
    initForm();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
