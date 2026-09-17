/* Veyon Control — panel y portal */
(function () {
  'use strict';

  var fmt = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 2 });

  function parseMoney(v) {
    var s = String(v || '').replace(/[^\d,.\-]/g, '');
    if (!s) return 0;
    var c = s.lastIndexOf(','), d = s.lastIndexOf('.');
    if (c > -1 && d > -1) {
      s = c > d ? s.replace(/\./g, '').replace(',', '.') : s.replace(/,/g, '');
    } else if (c > -1) {
      s = /,\d{1,2}$/.test(s) && s.split(',').length === 2 ? s.replace(',', '.') : s.replace(/,/g, '');
    } else if (d > -1) {
      s = /\.\d{1,2}$/.test(s) && s.split('.').length === 2 ? s : s.replace(/\./g, '');
    }
    var n = parseFloat(s);
    return isNaN(n) ? 0 : n;
  }
  function money(n) { return '$ ' + fmt.format(Math.round(n * 100) / 100); }

  // Menú lateral en móvil
  document.querySelectorAll('[data-toggle-side]').forEach(function (b) {
    b.addEventListener('click', function (e) {
      e.stopPropagation();
      document.body.classList.toggle('side-open');
    });
  });
  document.addEventListener('click', function (e) {
    if (document.body.classList.contains('side-open') && !e.target.closest('.side')) {
      document.body.classList.remove('side-open');
    }
  });

  // Confirmaciones
  document.addEventListener('submit', function (e) {
    var msg = e.target.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) e.preventDefault();
  });
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-confirm-click]');
    if (el && !window.confirm(el.getAttribute('data-confirm-click'))) e.preventDefault();
  });

  // Copiar al portapapeles
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-copy-target]');
    if (!btn) return;
    var input = document.getElementById(btn.getAttribute('data-copy-target'));
    if (!input) return;
    input.select();
    (navigator.clipboard ? navigator.clipboard.writeText(input.value) : Promise.reject()).catch(function () {
      document.execCommand('copy');
    });
    var old = btn.textContent;
    btn.textContent = 'Copiado';
    setTimeout(function () { btn.textContent = old; }, 1500);
  });

  // Autoenvío de filtros
  document.querySelectorAll('[data-autosubmit]').forEach(function (el) {
    el.addEventListener('change', function () { el.form.submit(); });
  });

  /* ---------- Editor de líneas de facturas y cotizaciones ---------- */
  var editor = document.querySelector('[data-items-editor]');
  if (editor) {
    var body = editor.querySelector('tbody');
    var tpl = document.getElementById('item-row-template');
    var catalog = {};
    try { catalog = JSON.parse(editor.getAttribute('data-catalog') || '{}'); } catch (err) {}

    var discountEl = document.querySelector('[name="discount"]');
    var taxEl = document.querySelector('[name="tax_rate"]');

    function recalc() {
      var subtotal = 0;
      body.querySelectorAll('tr').forEach(function (tr) {
        var q = parseMoney(tr.querySelector('[name="item_qty[]"]').value);
        var p = parseMoney(tr.querySelector('[name="item_price[]"]').value);
        var line = Math.round(q * p * 100) / 100;
        subtotal += line;
        tr.querySelector('.c-total').textContent = money(line);
      });
      var discount = Math.min(Math.max(parseMoney(discountEl && discountEl.value), 0), subtotal);
      var rate = Math.min(Math.max(parseMoney(taxEl && taxEl.value), 0), 100);
      var tax = Math.round((subtotal - discount) * rate) / 100;
      var set = function (sel, v) { var el = document.querySelector(sel); if (el) el.textContent = money(v); };
      set('[data-subtotal]', subtotal);
      set('[data-tax]', tax);
      set('[data-total]', subtotal - discount + tax);
    }

    function addRow(data) {
      var row = tpl.content.firstElementChild.cloneNode(true);
      if (data) {
        row.querySelector('[name="item_plan[]"]').value = data.plan || '';
        row.querySelector('[name="item_desc[]"]').value = data.desc || '';
        row.querySelector('[name="item_qty[]"]').value = data.qty || 1;
        row.querySelector('[name="item_price[]"]').value = data.price || 0;
      }
      body.appendChild(row);
      recalc();
      return row;
    }

    editor.addEventListener('input', recalc);
    if (discountEl) discountEl.addEventListener('input', recalc);
    if (taxEl) taxEl.addEventListener('input', recalc);

    editor.addEventListener('change', function (e) {
      if (e.target.name !== 'item_plan[]') return;
      var plan = catalog[e.target.value];
      if (!plan) return;
      var tr = e.target.closest('tr');
      tr.querySelector('[name="item_desc[]"]').value = plan.desc;
      tr.querySelector('[name="item_price[]"]').value = plan.price;
      recalc();
    });

    editor.addEventListener('click', function (e) {
      if (e.target.closest('[data-del-row]')) {
        e.target.closest('tr').remove();
        if (!body.children.length) addRow();
        recalc();
      }
    });

    document.querySelectorAll('[data-add-row]').forEach(function (b) {
      b.addEventListener('click', function () {
        var r = addRow();
        r.querySelector('[name="item_desc[]"]').focus();
      });
    });

    if (!body.children.length) addRow();
    recalc();
  }
})();
