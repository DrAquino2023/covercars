<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "covercars");
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

// Solo cargar productos si estamos en la página principal
$productos = null;
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page === 'index.php') {
  $productos = $conexion->query("SELECT * FROM productos ORDER BY nombre ASC");
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Covercars - Fundas a medida para tu auto. Más de 20 años de experiencia fabricando fundas únicas con materiales de alta calidad.">
  <meta name="keywords" content="fundas para autos, protección vehicular, fundas a medida, covercars">
  
  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  
  <!-- JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  
  <title>Covercars - Fundas a medida para tu auto</title>
  
  <script>
    // Variable global para JavaScript
    const usuarioLogueado = <?= isset($_SESSION['usuario']) ? 'true' : 'false' ?>;
    window.productoSeleccionado = null;
  </script>
</head>

<body data-usuario="<?= isset($_SESSION['usuario']) ? '1' : '0' ?>">

  <!-- Header Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm fixed-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="img/logo.png" alt="Covercars" width="100" class="me-2">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php#nosotros">Nosotros</a></li>
          <li class="nav-item"><a class="nav-link" href="productos.php">Productos</a></li>
          <!-- Enlace Admin Dashboard -->
          <?php if(isset($_SESSION['usuario']['is_admin']) && $_SESSION['usuario']['is_admin']): ?>
            <li class="nav-item"><a class="nav-link text-danger" href="admin/index.php">Dashboard</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="index.php#contacto">Contacto</a></li>

          <!-- BOTÓN CARRITO -->
          <li class="nav-item ms-3">
            <a href="carrito.php" class="btn btn-outline-warning rounded-circle position-relative d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
              <i class="bi bi-cart3 fs-5"></i>
              <span id="contador-carrito" class="badge bg-success position-absolute top-0 start-100 translate-middle rounded-pill">0</span>
            </a>
          </li>

          <?php if (isset($_SESSION['usuario'])): ?>
            <!-- USUARIO LOGUEADO -->
            <li class="nav-item dropdown ms-3">
              <a class="nav-link dropdown-toggle text-warning d-flex align-items-center" href="#" id="usuarioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-2 fs-5"></i>
                <span class="fw-semibold"><?= strtok($_SESSION['usuario']['nombre'], ' ') ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-3 mt-2" aria-labelledby="usuarioDropdown">
                <li><h6 class="dropdown-header">👋 Hola, <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></h6></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                <li><a class="dropdown-item" href="editar_perfil.php"><i class="bi bi-pencil-square me-2"></i> Editar Perfil</a></li>
                <li><a class="dropdown-item" href="historial.php"><i class="bi bi-clock-history me-2"></i> Mis Compras</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a></li>
              </ul>
            </li>
          <?php else: ?>
            <!-- USUARIO NO LOGUEADO -->
            <li class="nav-item ms-3">
              <button class="btn btn-outline-warning fw-semibold" data-bs-toggle="modal" data-bs-target="#loginModal">
                <i class="bi bi-person-plus me-1"></i> Iniciar sesión
              </button>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Modal Login / Registro / Recuperar -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-dark text-white border-0">
          <h5 class="modal-title" id="loginModalLabel">
            <i class="bi bi-car-front me-2"></i>Bienvenido a <strong class="text-warning">Covercars</strong>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body bg-light p-4">
          <!-- Navegación de pestañas -->
          <ul class="nav nav-pills justify-content-center mb-4" id="loginTabs" role="tablist">
            <li class="nav-item">
              <button class="nav-link active px-4" id="login-tab" data-bs-toggle="pill" data-bs-target="#login" type="button" role="tab">
                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link px-4" id="register-tab" data-bs-toggle="pill" data-bs-target="#register" type="button" role="tab">
                <i class="bi bi-person-plus me-1"></i> Registrarse
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link px-4" id="recover-tab" data-bs-toggle="pill" data-bs-target="#recover" type="button" role="tab">
                <i class="bi bi-key me-1"></i> Recuperar
              </button>
            </li>
          </ul>

          <!-- Contenido de las pestañas -->
          <div class="tab-content">
            <!-- Iniciar sesión -->
            <div class="tab-pane fade show active" id="login" role="tabpanel">
              <form action="procesar_login.php" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                  <label for="emailLogin" class="form-label fw-semibold">
                    <i class="bi bi-envelope me-1"></i> Correo electrónico
                  </label>
                  <input type="email" class="form-control form-control-lg" id="emailLogin" name="email" required>
                  <div class="invalid-feedback">Por favor ingresa un email válido.</div>
                </div>
                <div class="mb-4">
                  <label for="passwordLogin" class="form-label fw-semibold">
                    <i class="bi bi-lock me-1"></i> Contraseña
                  </label>
                  <input type="password" class="form-control form-control-lg" id="passwordLogin" name="password" required>
                  <div class="invalid-feedback">La contraseña es obligatoria.</div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                  <i class="bi bi-box-arrow-in-right me-2"></i> Ingresar
                </button>
              </form>
            </div>

            <!-- Registrarse -->
            <div class="tab-pane fade" id="register" role="tabpanel">
              <form action="procesar_registro.php" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                  <label for="nombre" class="form-label fw-semibold">
                    <i class="bi bi-person me-1"></i> Nombre completo
                  </label>
                  <input type="text" class="form-control form-control-lg" id="nombre" name="nombre" required>
                  <div class="invalid-feedback">El nombre es obligatorio.</div>
                </div>
                <div class="mb-3">
                  <label for="emailRegistro" class="form-label fw-semibold">
                    <i class="bi bi-envelope me-1"></i> Correo electrónico
                  </label>
                  <input type="email" class="form-control form-control-lg" id="emailRegistro" name="email" required>
                  <div class="invalid-feedback">Por favor ingresa un email válido.</div>
                </div>
                <div class="mb-4">
                  <label for="passwordRegistro" class="form-label fw-semibold">
                    <i class="bi bi-lock me-1"></i> Contraseña
                  </label>
                  <input type="password" class="form-control form-control-lg" id="passwordRegistro" name="password" minlength="6" required>
                  <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                </div>
                <button type="submit" class="btn btn-success btn-lg w-100 fw-semibold">
                  <i class="bi bi-person-plus me-2"></i> Registrarse
                </button>
              </form>
            </div>

            <!-- Recuperar contraseña -->
            <div class="tab-pane fade" id="recover" role="tabpanel">
              <form action="recuperar_contrasena.php" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                  <label for="emailRecuperar" class="form-label fw-semibold">
                    <i class="bi bi-envelope me-1"></i> Correo electrónico
                  </label>
                  <input type="email" class="form-control form-control-lg" id="emailRecuperar" name="email" required>
                  <div class="invalid-feedback">Por favor ingresa un email válido.</div>
                </div>
                <div class="mb-3">
                  <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i> 
                    Te enviaremos un enlace para restablecer tu contraseña.
                  </small>
                </div>
                <button type="submit" class="btn btn-warning btn-lg w-100 fw-semibold">
                  <i class="bi bi-envelope-paper me-2"></i> Enviar recuperación
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast para mensajes -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toastCarrito" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body fw-semibold">
          <i class="bi bi-check-circle me-2"></i> Producto agregado al carrito
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
      </div>
    </div>
  </div>

  <script>
    // Validación de formularios Bootstrap
    (function() {
      'use strict';
      window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();
  </script>