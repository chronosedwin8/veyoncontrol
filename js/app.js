/* ============================================================
   Veyon Control - JavaScript del sitio público (sin dependencias)
   ============================================================ */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------------------------------------------------
     1. Navegación, barra de progreso y botón "arriba"
        Un único listener de scroll agrupado con requestAnimationFrame
  --------------------------------------------------------- */
  function initScrollUI() {
    var nav = document.querySelector('.nav');
    var bar = document.querySelector('.scroll-progress');
    var top = document.querySelector('.to-top');
    var ticking = false;

    function update() {
      ticking = false;
      var y = window.scrollY;
      var h = document.documentElement.scrollHeight - window.innerHeight;
      if (nav) nav.classList.toggle('scrolled', y > 20);
      if (bar) bar.style.transform = 'scaleX(' + (h > 0 ? y / h : 0) + ')';
      if (top) top.classList.toggle('show', y > 600);
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; requestAnimationFrame(update); }
    }, { passive: true });
    update();

    if (top) {
      top.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
      });
    }
  }

  function initBurger() {
    var burger = document.querySelector('.burger');
    var links = document.querySelector('.nav-links');
    if (!burger || !links) return;

    function close() {
      document.body.classList.remove('nav-open');
      burger.setAttribute('aria-expanded', 'false');
    }
    burger.addEventListener('click', function () {
      var open = document.body.classList.toggle('nav-open');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    links.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') close();
    });
    window.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') close();
    });
  }

  /* ---------------------------------------------------------
     2. Aparición progresiva
  --------------------------------------------------------- */
  function initReveal() {
    var items = document.querySelectorAll('.reveal:not(.in)');
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
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    items.forEach(function (el) { io.observe(el); });
  }

  /* ---------------------------------------------------------
     3. Contadores animados
  --------------------------------------------------------- */
  function initCounters() {
    var nums = document.querySelectorAll('[data-count]');
    if (!nums.length) return;

    function final(el) {
      return parseFloat(el.getAttribute('data-count')).toFixed(el.getAttribute('data-decimals') | 0) + (el.getAttribute('data-suffix') || '');
    }
    if (!('IntersectionObserver' in window) || reduceMotion) {
      nums.forEach(function (el) { el.textContent = final(el); });
      return;
    }
    function run(el) {
      var target = parseFloat(el.getAttribute('data-count'));
      var suffix = el.getAttribute('data-suffix') || '';
      var decimals = el.getAttribute('data-decimals') | 0;
      var t0 = performance.now();
      (function frame(now) {
        var p = Math.min((now - t0) / 1400, 1);
        el.textContent = (target * (1 - Math.pow(1 - p, 3))).toFixed(decimals) + suffix;
        if (p < 1) requestAnimationFrame(frame);
      })(t0);
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { run(entry.target); io.unobserve(entry.target); }
      });
    }, { threshold: 0.5 });
    nums.forEach(function (el) { io.observe(el); });
  }

  /* ---------------------------------------------------------
     4. Pestañas
  --------------------------------------------------------- */
  function initTabs() {
    document.querySelectorAll('[data-tabs]').forEach(function (group) {
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
          panels.forEach(function (p) { p.classList.toggle('active', p.id === id); });
        });
      });
    });
  }

  /* ---------------------------------------------------------
     5. Efecto de escritura en el titular (se pausa fuera de pantalla)
  --------------------------------------------------------- */
  function initTyping() {
    var el = document.querySelector('[data-typing]');
    if (!el) return;
    var words;
    try { words = JSON.parse(el.getAttribute('data-typing')); } catch (err) { return; }
    if (!words || !words.length || reduceMotion) return;

    var w = 0, c = words[0].length, deleting = true, visible = true;
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (e) { visible = e[0].isIntersecting; }).observe(el);
    }
    function loop() {
      if (!visible || document.hidden) { setTimeout(loop, 600); return; }
      var word = words[w];
      c += deleting ? -1 : 1;
      el.textContent = word.slice(0, c) || ' ';
      var delay = deleting ? 45 : 85;
      if (!deleting && c === word.length) { deleting = true; delay = 1900; }
      else if (deleting && c === 0) { deleting = false; w = (w + 1) % words.length; delay = 300; }
      setTimeout(loop, delay);
    }
    setTimeout(loop, 2200);
  }

  /* ---------------------------------------------------------
     6. Detección del sistema operativo (descargas)
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
    var all = { url: '#windows', label: 'Ver todos los paquetes' };
    var map = {
      win64: { name: 'Windows (64 bits) detectado', desc: 'Instalador recomendado: veyon-' + BUILD + '-win64-setup.exe · versión ' + RELEASE, url: BASE + 'veyon-' + BUILD + '-win64-setup.exe', label: 'Descargar para Windows 64 bits' },
      win32: { name: 'Windows (32 bits) detectado', desc: 'Instalador recomendado: veyon-' + BUILD + '-win32-setup.exe · versión ' + RELEASE, url: BASE + 'veyon-' + BUILD + '-win32-setup.exe', label: 'Descargar para Windows 32 bits' },
      ubuntu: { name: 'Ubuntu detectado', desc: 'Paquete .deb para Ubuntu 24.04 LTS. Si usas otra versión, revisa la tabla de Linux.', url: BASE + 'veyon_' + BUILD + '-ubuntu.24.04_amd64.deb', label: 'Descargar .deb para Ubuntu 24.04' },
      debian: { name: 'Debian detectado', desc: 'Paquete .deb para Debian 12. Si usas otra versión, revisa la tabla de Linux.', url: BASE + 'veyon_' + BUILD + '-debian.12_amd64.deb', label: 'Descargar .deb para Debian 12' },
      fedora: { name: 'Fedora detectado', desc: 'Paquete .rpm para Fedora 43. Si usas otra versión, revisa la tabla de Linux.', url: BASE + 'veyon-' + BUILD + '-fedora.43.x86_64.rpm', label: 'Descargar .rpm para Fedora 43' },
      linux: { name: 'Linux detectado', desc: 'Elige tu distribución en la tabla de paquetes: Debian, Ubuntu, Fedora, openSUSE, RHEL o Rocky Linux.', url: '#linux', label: 'Ver paquetes para Linux' },
      mac: { name: 'macOS detectado', desc: 'No hay instalador para macOS. Escríbenos y revisamos alternativas para tu aula.', url: 'index.php#contacto', label: 'Hablar con nosotros' },
      android: { name: 'Android detectado', desc: 'Veyon es software de escritorio. Descarga el instalador desde el equipo donde vayas a usarlo.', url: all.url, label: all.label },
      ios: { name: 'iOS detectado', desc: 'Veyon es software de escritorio. Descarga el instalador desde el equipo donde vayas a usarlo.', url: all.url, label: all.label },
      otro: { name: 'Sistema no identificado', desc: 'Selecciona manualmente el paquete que corresponda a tu sistema operativo.', url: all.url, label: all.label }
    };
    var info = map[detectOS()] || map.otro;
    if (nameEl) nameEl.textContent = info.name;
    if (descEl) descEl.textContent = info.desc;
    if (btn) {
      btn.href = info.url;
      var span = btn.querySelector('span');
      if (span) span.textContent = info.label;
    }
  }

  /* ---------------------------------------------------------
     7. Filtro de paquetes Linux
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
     8. Copiar al portapapeles
  --------------------------------------------------------- */
  function initCopy() {
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var src = document.querySelector(btn.getAttribute('data-copy'));
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
     9. Menú desplegable "Recursos"
  --------------------------------------------------------- */
  function initSubmenu() {
    var groups = document.querySelectorAll('.has-sub');
    if (!groups.length) return;
    var closeAll = function (except) {
      groups.forEach(function (g) {
        if (g === except) return;
        g.classList.remove('open');
        var t = g.querySelector('.sub-toggle');
        if (t) t.setAttribute('aria-expanded', 'false');
      });
    };
    groups.forEach(function (group) {
      var toggle = group.querySelector('.sub-toggle');
      if (!toggle) return;
      toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        var open = group.classList.toggle('open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        closeAll(group);
      });
      group.addEventListener('mouseenter', function () { if (window.innerWidth > 1180) group.classList.add('open'); });
      group.addEventListener('mouseleave', function () { if (window.innerWidth > 1180) group.classList.remove('open'); });
    });
    document.addEventListener('click', function () { closeAll(null); });
    window.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAll(null); });
  }

  /* ---------------------------------------------------------
     10. Selector de moneda (tasa EUR administrable desde el panel)
  --------------------------------------------------------- */
  var eurRate = parseFloat(document.body.getAttribute('data-eur-rate')) || 5000;
  var CURRENCIES = {
    COP: { rate: 1, sym: '$', code: 'COP', locale: 'es-CO', before: true },
    EUR: { rate: eurRate, sym: '€', code: 'EUR', locale: 'es-ES', before: false }
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
      txt = value.toLocaleString(c.locale, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    } catch (e) {
      txt = String(Math.round(value));
    }
    return c.before ? (c.sym + ' ' + txt) : (txt + ' ' + c.sym);
  }

  function applyCurrency(cur) {
    var c = CURRENCIES[cur] ? cur : 'COP';
    document.querySelectorAll('[data-cop]').forEach(function (el) {
      var cop = parseFloat(el.getAttribute('data-cop'));
      if (!isNaN(cop)) el.textContent = formatMoney(cop, c);
    });
    document.querySelectorAll('[data-cur-code]').forEach(function (el) { el.textContent = CURRENCIES[c].code; });
    document.querySelectorAll('.cur-switch button').forEach(function (btn) {
      var on = btn.getAttribute('data-cur') === c;
      btn.classList.toggle('on', on);
      btn.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
    try { localStorage.setItem(CUR_KEY, c); } catch (e) {}
  }

  function initCurrency() {
    applyCurrency(readCurrency());
    document.querySelectorAll('.cur-switch button').forEach(function (btn) {
      btn.addEventListener('click', function () { applyCurrency(btn.getAttribute('data-cur')); });
    });
  }

  /* ---------------------------------------------------------
     11. Formulario de cotización (se guarda en el panel de administración)
  --------------------------------------------------------- */
  function initForm() {
    var form = document.getElementById('form-cotizacion');
    if (!form) return;
    var note = document.getElementById('form-msg');
    var submit = form.querySelector('[type="submit"]');
    var required = ['f-inst', 'f-nombre', 'f-mail', 'f-equipos'];

    function mark(el, invalid) {
      var wrap = el.closest('.field');
      if (wrap) wrap.classList.toggle('invalid', invalid);
      el.setAttribute('aria-invalid', invalid ? 'true' : 'false');
    }
    function say(msg, cls) {
      if (!note) return;
      note.textContent = msg;
      note.classList.remove('ok', 'bad');
      if (cls) note.classList.add(cls);
    }

    // "Solicitar propuesta" en un plan: preselecciona la cantidad de equipos
    document.querySelectorAll('[data-plan-request]').forEach(function (a) {
      a.addEventListener('click', function () {
        var sel = document.getElementById('f-equipos');
        if (!sel) return;
        var want = a.getAttribute('data-plan-request');
        Array.prototype.forEach.call(sel.options, function (o) { if (o.text === want) sel.value = o.value || o.text; });
      });
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var firstBad = null;
      required.forEach(function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        var v = el.value.trim();
        var bad = !v || (id === 'f-mail' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v));
        mark(el, bad);
        if (bad && !firstBad) firstBad = el;
      });
      if (firstBad) {
        say('Revisa los campos marcados antes de enviar.', 'bad');
        firstBad.focus();
        return;
      }

      submit.disabled = true;
      say('Enviando…');
      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
      })
        .then(function (r) { return r.json().catch(function () { return { ok: false, message: 'Respuesta inesperada del servidor.' }; }); })
        .then(function (res) {
          if (res.ok) {
            form.reset();
            say(res.message, 'ok');
          } else {
            say(res.message || 'No se pudo enviar la solicitud.', 'bad');
          }
        })
        .catch(function () { say('No hay conexión. Inténtalo de nuevo en unos segundos.', 'bad'); })
        .then(function () { submit.disabled = false; });
    });

    form.addEventListener('input', function (e) {
      if (e.target.closest('.field')) mark(e.target, false);
    });
  }

  /* ---------------------------------------------------------
     12. Video de YouTube bajo demanda (no carga ~1 MB hasta hacer clic)
  --------------------------------------------------------- */
  function initVideo() {
    document.querySelectorAll('.video-poster[data-yt]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var iframe = document.createElement('iframe');
        iframe.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(btn.getAttribute('data-yt')) + '?autoplay=1&rel=0';
        iframe.title = btn.getAttribute('data-title') || 'Video';
        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
        iframe.referrerPolicy = 'strict-origin-when-cross-origin';
        iframe.allowFullscreen = true;
        btn.replaceWith(iframe);
      });
    });
  }

  function boot() {
    initScrollUI();
    initBurger();
    initReveal();
    initCounters();
    initTabs();
    initTyping();
    initOSDetect();
    initFilter();
    initCopy();
    initSubmenu();
    initCurrency();
    initForm();
    initVideo();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
