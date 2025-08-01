<section id="contacto" class="bg-white">
  <div class="container">
    <h2 class="text-center mb-5" data-aos="fade-down">Contacto</h2>
    <div class="row">
      <!-- FORMULARIO -->
      <div class="col-md-6 mb-4" data-aos="fade-right">
        <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert" id="alerta-contacto">
            <strong>¡Mensaje enviado!</strong> Gracias por contactarte con Covercars. Te responderemos pronto.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 1): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alerta-contacto">
            <strong>Error al enviar el mensaje.</strong> Por favor intentá nuevamente más tarde.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
        <?php endif; ?>

        <form action="enviar.php" method="POST">
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="mensaje" class="form-label">Mensaje</label>
            <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Enviar mensaje</button>
        </form>
      </div>

      <!-- DATOS + MAPA -->
      <div class="col-md-6" data-aos="fade-left">
        <h5 class="fw-bold">Covercars</h5>
        <p>📍 Yerbal 4711, Buenos Aires, Argentina</p>
        <p>📞 (011) 5555-5555</p>
        <p>📧 contacto@covercars.com.ar</p>
        <p>🕒 Lunes a viernes de 9 a 18 hs</p>

        <div class="ratio ratio-16x9 mt-3">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d820.6920026163345!2d-58.49328033034326!3d-34.63530239486616!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95bcc9518f96966d%3A0xc0b1a3b528ee317c!2sCoverCars!5e0!3m2!1ses!2sar!4v1742743477041!5m2!1ses!2sar" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </div>
</section>
