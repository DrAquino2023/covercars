<?php include "layout.php"; ?>
<?php require_once "../conexion.php"; ?>
<?php
$id = $_GET['id'] ?? null;
$nombre = $email = '';
$is_admin = 0;

if ($id) {
    $stmt = $conn->prepare("SELECT nombre, email, is_admin FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nombre, $email, $is_admin);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = trim($_POST['nombre']);
    $e = trim($_POST['email']);
    $a = isset($_POST['is_admin']) ? 1 : 0;
    if ($id) {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, email=?, is_admin=? WHERE id=?");
        $stmt->bind_param("ssii", $n, $e, $a, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, is_admin) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $n, $e, $a);
    }
    $stmt->execute();
    header("Location: usuarios.php");
    exit;
}
?>
<div class="content-header mb-3">
  <h1><?= $id ? "Editar Usuario" : "Nuevo Usuario" ?></h1>
</div>
<div class="card">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
      </div>
      <div class="col-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" <?= $is_admin?'checked':'' ?>>
          <label class="form-check-label" for="is_admin">Administrador</label>
        </div>
      </div>
      <div class="col-12 text-end">
        <button class="btn btn-primary"><?= $id ? "Actualizar" : "Crear" ?></button>
        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php include "footer_layout.php"; ?>