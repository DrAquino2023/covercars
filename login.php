<!-- login.php: Ventana modal de login / registro / recuperación -->

<!-- Modal -->
<div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalLoginLabel">Ingresar a Covercars</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Nav de pestañas -->
        <ul class="nav nav-tabs mb-3" id="tabsLogin" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Ingresar</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Registrarse</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="recover-tab" data-bs-toggle="tab" data-bs-target="#recover" type="button" role="tab">Recuperar</button>
          </li>
        </ul>

        <!-- Cuerpo de pestañas -->
        <div class="tab-content">
          <!-- Login -->
          <div class="tab-pane fade show active" id="login" role="tabpanel">
            <form action="procesar_login.php" method="POST">
              <div class="mb-3">
                <label for="login-email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="login-email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="login-password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="login-password" name="password" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
            </form>
          </div>

          <!-- Registro -->
          <div class="tab-pane fade" id="register" role="tabpanel">
            <form action="procesar_registro.php" method="POST">
              <div class="mb-3">
                <label for="register-nombre" class="form-label">Nombre completo</label>
                <input type="text" class="form-control" id="register-nombre" name="nombre" required>
              </div>
              <div class="mb-3">
                <label for="register-email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="register-email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="register-password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="register-password" name="password" required>
              </div>
              <button type="submit" class="btn btn-success w-100">Registrarse</button>
            </form>
          </div>

          <!-- Recuperación -->
          <div class="tab-pane fade" id="recover" role="tabpanel">
            <form action="recuperar_contrasena.php" method="POST">
              <div class="mb-3">
                <label for="recover-email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="recover-email" name="email" required>
              </div>
              <button type="submit" class="btn btn-warning w-100">Enviar enlace de recuperación</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
