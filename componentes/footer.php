<!-- FOOTER -->

<footer class="bg-dark text-white text-center py-3">
  <p class="mb-0">© 2025 Covercars. Todos los derechos reservados.</p>
</footer>

<!-- TOAST Agregado al carrito -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
  <div id="toastCarrito" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Producto agregado al carrito.
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>
  </div>
</div>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
  <div id="toastMensaje" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastMensajeTexto">
        <!-- Mensaje dinámico -->
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>
  </div>
</div>
<!-- Scripts -->

<script src="../js/script.js"></script>
<?php
  $page = basename($_SERVER['PHP_SELF']);
  if (in_array($page, ['index.php', 'productos.php'])): ?>
    <script src="../js/carrito.js"></script>
<?php endif; ?>

<script>
  AOS.init({ duration: 1000, once: true });
</script>
</body>
</html>
