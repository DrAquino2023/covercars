<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
$usuario = $_SESSION['usuario'];
?>

<?php include "componentes/header.php"; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-10">

      <div class="card shadow-sm border-0 mb-4 mt-5">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
          <h4 class="mb-0">👤 Mi Perfil</h4>
          <a href="editar_perfil.php" class="btn btn-outline-light btn-sm">Editar perfil</a>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-4">
              <strong>Nombre completo</strong>
              <p><?= htmlspecialchars($usuario['nombre']) ?></p>
            </div>
            <div class="col-md-4">
              <strong>Email</strong>
              <p><?= htmlspecialchars($usuario['email']) ?></p>
            </div>
            <div class="col-md-4">
              <strong>Fecha de nacimiento</strong>
              <p><?= htmlspecialchars($usuario['nacimiento'] ?? 'No informado') ?></p>
            </div>
          </div>

          <hr>
          <div class="text-end">
            <a href="index.php" class="btn btn-secondary">Volver al inicio</a>
            <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<?php include "componentes/footer.php"; ?>
