<?php include "layout.php"; ?>
<?php require_once "../conexion.php"; 

$pedido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$pedido_id) {
  echo '<div class="alert alert-danger">ID de pedido no válido.</div>';
  include "footer_layout.php";
  exit;
}

// Obtener información del pedido
$stmt = $conn->prepare("
  SELECT p.id, u.nombre AS cliente, u.email, p.fecha, p.status
  FROM pedidos p
  JOIN usuarios u ON p.usuario_id = u.id
  WHERE p.id = ?
");
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$res = $stmt->get_result();
$pedido = $res->fetch_assoc();
$stmt->close();

if (!$pedido) {
  echo '<div class="alert alert-danger">Pedido no encontrado.</div>';
  include "footer_layout.php";
  exit;
}

// Calcular total
$total = 0;
$stmt = $conn->prepare("
  SELECT SUM(precio_unitario * cantidad) as total
  FROM detalle_pedido
  WHERE pedido_id = ?
");
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$total = $row['total'] ?? 0;
$stmt->close();
?>

<div class="content-header mb-3">
  <h1 class="m-0">Detalle del Pedido #<?= $pedido['id'] ?></h1>
</div>

<div class="row">
  <!-- Información del pedido -->
  <div class="col-md-6">
    <div class="card mb-4">
      <div class="card-header">
        <h3 class="card-title">Información del Pedido</h3>
      </div>
      <div class="card-body">
        <table class="table table-sm">
          <tr>
            <th width="30%">Cliente:</th>
            <td><?= htmlspecialchars($pedido['cliente']) ?></td>
          </tr>
          <tr>
            <th>Email:</th>
            <td><?= htmlspecialchars($pedido['email']) ?></td>
          </tr>
          <tr>
            <th>Fecha:</th>
            <td><?= date('d/m/Y H:i:s', strtotime($pedido['fecha'])) ?></td>
          </tr>
          <tr>
            <th>Estado:</th>
            <td>
              <?php
              $statusClass = [
                'pending' => 'badge-warning',
                'approved' => 'badge-success',
                'failure' => 'badge-danger'
              ][$pedido['status']] ?? 'badge-secondary';
              $statusText = [
                'pending' => 'Pendiente',
                'approved' => 'Aprobado',
                'failure' => 'Rechazado'
              ][$pedido['status']] ?? $pedido['status'];
              ?>
              <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
            </td>
          </tr>
          <tr>
            <th>Total:</th>
            <td><strong class="text-primary">$<?= number_format($total, 2, ',', '.') ?></strong></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Acciones -->
  <div class="col-md-6">
    <div class="card mb-4">
      <div class="card-header">
        <h3 class="card-title">Acciones</h3>
      </div>
      <div class="card-body">
        <form id="updateStatusForm" class="mb-3">
          <div class="form-group">
            <label>Cambiar Estado:</label>
            <select class="form-control" id="newStatus" name="status">
              <option value="pending" <?= $pedido['status']=='pending'?'selected':'' ?>>Pendiente</option>
              <option value="approved" <?= $pedido['status']=='approved'?'selected':'' ?>>Aprobado</option>
              <option value="failure" <?= $pedido['status']=='failure'?'selected':'' ?>>Rechazado</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Actualizar Estado
          </button>
        </form>
        
        <a href="pedidos.php" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Volver a Pedidos
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Productos del pedido -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Productos del Pedido</h3>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $stmt = $conn->prepare("
            SELECT pr.nombre, dp.cantidad, dp.precio_unitario
            FROM detalle_pedido dp
            JOIN productos pr ON dp.producto_id = pr.id
            WHERE dp.pedido_id = ?
          ");
          $stmt->bind_param("i", $pedido_id);
          $stmt->execute();
          $res = $stmt->get_result();
          
          $total_general = 0;
          while($item = $res->fetch_assoc()):
            $subtotal = $item['cantidad'] * $item['precio_unitario'];
            $total_general += $subtotal;
          ?>
          <tr>
            <td><?= htmlspecialchars($item['nombre']) ?></td>
            <td class="text-center"><?= $item['cantidad'] ?></td>
            <td class="text-right">$<?= number_format($item['precio_unitario'], 2, ',', '.') ?></td>
            <td class="text-right">$<?= number_format($subtotal, 2, ',', '.') ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-right">Total:</th>
            <th class="text-right">$<?= number_format($total_general, 2, ',', '.') ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<script>
$(function() {
  // Actualizar estado
  $('#updateStatusForm').on('submit', function(e) {
    e.preventDefault();
    
    const newStatus = $('#newStatus').val();
    
    $.ajax({
      url: 'update_status.php',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        pedido_id: <?= $pedido_id ?>,
        status: newStatus
      }),
      success: function(resp) {
        alert('Estado actualizado correctamente');
        location.reload();
      },
      error: function() {
        alert('Error al actualizar el estado');
      }
    });
  });
});
</script>

<?php include "footer_layout.php"; ?>