<?php include "layout.php"; ?>
<?php require_once "../conexion.php"; ?>
<?php
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: usuarios.php");
    exit;
}
$res = $conn->query("SELECT id, nombre, email, is_admin FROM usuarios");
?>
<div class="content-header mb-3">
  <h1>Usuarios</h1>
  <a href="usuario_form.php" class="btn btn-success btn-sm float-right mb-2">
    <i class="fas fa-plus"></i> Nuevo Usuario
  </a>
</div>
<div class="card">
  <div class="card-body">
    <table id="usuariosTable" class="table table-striped table-bordered">
      <thead>
        <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Admin</th><th>Acciones</th></tr>
      </thead>
      <tbody>
        <?php while($u = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['nombre']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= $u['is_admin']?'Sí':'No' ?></td>
          <td>
            <a href="usuario_form.php?id=<?= $u['id'] ?>" class="btn btn-info btn-sm">
              <i class="fas fa-edit"></i>
            </a>
            <a href="usuarios.php?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar usuario?')">
              <i class="fas fa-trash"></i>
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<script>
$(function(){
  $('#usuariosTable').DataTable({responsive:true,autoWidth:false});
});
</script>
<?php include "footer_layout.php"; ?>