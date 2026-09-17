</main>

<footer>
  <div class="container">
    <div class="foot-grid">
      <div class="foot-brand">
        <a class="brand" href="<?= e(url('index.php')) ?>">
          <img src="<?= e(url('assets/img/logo.svg')) ?>" alt="" width="34" height="34" loading="lazy">
          <span>Veyon<b>Control</b></span>
        </a>
        <p>Licenciamiento, actualizaciones permanentes y soporte profesional de Veyon para aulas, laboratorios y salas de formación.</p>
        <div class="socials">
          <a href="https://youtu.be/MP0ypeqDndM" target="_blank" rel="noopener" aria-label="Video tutorial en YouTube"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 12s0-3.8-.5-5.6c-.3-1-1-1.8-2-2.1C18.7 3.8 12 3.8 12 3.8s-6.7 0-8.5.5c-1 .3-1.7 1.1-2 2.1C1 8.2 1 12 1 12s0 3.8.5 5.6c.3 1 1 1.8 2 2.1 1.8.5 8.5.5 8.5.5s6.7 0 8.5-.5c1-.3 1.7-1.1 2-2.1.5-1.8.5-5.6.5-5.6zM9.8 15.5v-7l6.2 3.5-6.2 3.5z"/></svg></a>
          <a href="https://veyon.nodebb.com" target="_blank" rel="noopener" aria-label="Foro"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></a>
          <a href="https://docs.veyon.io/es/latest/" target="_blank" rel="noopener" aria-label="Documentación"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg></a>
        </div>
      </div>
      <div class="foot-col">
        <h4>Plataforma</h4>
        <a href="<?= e(url('index.php#funciones')) ?>">Funciones</a>
        <a href="<?= e(url('index.php#capturas')) ?>">Capturas</a>
        <a href="<?= e(url('index.php#video')) ?>">Video tutorial</a>
        <a href="<?= e(url('descargas.php')) ?>">Descargas</a>
        <a href="<?= e(url('complementos.php')) ?>">Complementos</a>
      </div>
      <div class="foot-col">
        <h4>Licencias</h4>
        <a href="<?= e(url('index.php#planes')) ?>">Planes y precios</a>
        <?php foreach (plans_catalog(true) as $fp): ?>
          <a href="<?= e(url('index.php#plan-' . $fp['slug'])) ?>"><?= e($fp['capacity_label'] ?: $fp['name']) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="foot-col">
        <h4>Clientes</h4>
        <a href="<?= e(url('portal/login.php')) ?>">Portal de clientes</a>
        <a href="<?= e(url('portal/registro.php')) ?>">Crear cuenta</a>
        <a href="<?= e(url('participa.php')) ?>">Soporte y recursos</a>
        <a href="<?= e(url('acerca.php')) ?>">Nosotros</a>
        <a href="<?= e(url('index.php#faq')) ?>">Preguntas frecuentes</a>
      </div>
    </div>

    <div class="foot-bottom">
      <span>© <?= date('Y') ?> <?= e(setting('company_name', 'Veyon Control')) ?> · Todos los precios en COP salvo indicación contraria</span>
      <div class="langs">
        <span class="muted">Moneda:</span>
        <div class="cur-switch" role="group" aria-label="Selector de moneda">
          <button type="button" data-cur="COP">COP</button>
          <button type="button" data-cur="EUR">EUR</button>
        </div>
      </div>
    </div>
  </div>
</footer>

<button class="to-top" aria-label="Volver arriba">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
</button>

<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
