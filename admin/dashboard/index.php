<?php 
// Activar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "layout.php"; 
?>

<?php
// Verificar conexión a base de datos
if (!isset($conn)) {
    echo '<div class="alert alert-danger">Error: No se pudo establecer conexión con la base de datos</div>';
    include "footer_layout.php";
    exit;
}

try {
    // Métricas con manejo de errores
    $totalOrders = 0;
    $pendingOrders = 0;
    $totalRevenue = 0;
    $totalProducts = 0;
    $totalUsers = 0;
    
    // Consultar total de pedidos
    $result = $conn->query("SELECT COUNT(*) as total FROM pedidos");
    if ($result) {
        $row = $result->fetch_assoc();
        $totalOrders = $row['total'] ?? 0;
    }
    
    // Consultar pedidos pendientes
    $result = $conn->query("SELECT COUNT(*) as total FROM pedidos WHERE status='pending'");
    if ($result) {
        $row = $result->fetch_assoc();
        $pendingOrders = $row['total'] ?? 0;
    }
    
    // Consultar ingresos totales
    $result = $conn->query("
        SELECT IFNULL(SUM(dp.precio_unitario * dp.cantidad),0) as total
        FROM pedidos p
        JOIN detalle_pedido dp ON p.id = dp.pedido_id
        WHERE p.status = 'approved'
    ");
    if ($result) {
        $row = $result->fetch_assoc();
        $totalRevenue = $row['total'] ?? 0;
    }
    
    // Consultar total de productos
    $result = $conn->query("SELECT COUNT(*) as total FROM productos");
    if ($result) {
        $row = $result->fetch_assoc();
        $totalProducts = $row['total'] ?? 0;
    }
    
    // Consultar total de usuarios
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    if ($result) {
        $row = $result->fetch_assoc();
        $totalUsers = $row['total'] ?? 0;
    }
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error al obtener datos: ' . $e->getMessage() . '</div>';
}
?>

<!-- Header Section -->
<div class="content-header mb-4">
  <div class="row align-items-center">
    <div class="col-sm-6">
      <h1 class="m-0 text-dark">Dashboard</h1>
      <p class="text-muted mb-0">Bienvenido al panel de administración</p>
    </div>
    <div class="col-sm-6 text-right">
      <small class="text-muted">
        <i class="far fa-clock mr-1"></i>
        <?= date('d/m/Y H:i') ?>
      </small>
    </div>
  </div>
</div>

<!-- Stats Cards -->
<div class="row">
  <!-- Total Pedidos -->
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon bg-primary">
        <i class="fas fa-shopping-cart"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Total Pedidos</span>
        <span class="info-box-number"><?= number_format($totalOrders) ?></span>
        <a href="pedidos.php" class="small-box-footer">
          Ver todos <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
  </div>
  
  <!-- Ingresos -->
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon bg-success">
        <i class="fas fa-dollar-sign"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Ingresos Totales</span>
        <span class="info-box-number">$<?= number_format($totalRevenue,2,',','.') ?></span>
        <a href="reportes.php" class="small-box-footer">
          Ver reportes <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
  </div>
  
  <!-- Productos -->
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon bg-warning">
        <i class="fas fa-box"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Productos</span>
        <span class="info-box-number"><?= number_format($totalProducts) ?></span>
        <a href="productos.php" class="small-box-footer">
          Gestionar <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
  </div>
  
  <!-- Usuarios -->
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon bg-info">
        <i class="fas fa-users"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Usuarios</span>
        <span class="info-box-number"><?= number_format($totalUsers) ?></span>
        <a href="usuarios.php" class="small-box-footer">
          Ver usuarios <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Tabla simple de últimos pedidos -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Últimos Pedidos</h3>
        <?php if($pendingOrders > 0): ?>
        <span class="badge badge-warning float-right"><?= $pendingOrders ?> pendientes</span>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Fecha</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
            try {
                $recentOrders = $conn->query("
                    SELECT p.id, u.nombre AS usuario, p.fecha, p.status
                    FROM pedidos p
                    JOIN usuarios u ON p.usuario_id = u.id
                    ORDER BY p.fecha DESC
                    LIMIT 5
                ");
                
                if ($recentOrders && $recentOrders->num_rows > 0) {
                    while($order = $recentOrders->fetch_assoc()):
                    ?>
                    <tr>
                      <td>#<?= $order['id'] ?></td>
                      <td><?= htmlspecialchars($order['usuario']) ?></td>
                      <td><?= date('d/m/Y H:i', strtotime($order['fecha'])) ?></td>
                      <td>
                        <?php
                        $statusClass = [
                          'pending' => 'badge-warning',
                          'approved' => 'badge-success',
                          'failure' => 'badge-danger'
                        ][$order['status']] ?? 'badge-secondary';
                        $statusText = [
                          'pending' => 'Pendiente',
                          'approved' => 'Aprobado',
                          'failure' => 'Rechazado'
                        ][$order['status']] ?? $order['status'];
                        ?>
                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                      </td>
                      <td>
                        <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                          <i class="fas fa-eye"></i> Ver
                        </a>
                      </td>
                    </tr>
                    <?php
                    endwhile;
                } else {
                    echo '<tr><td colspan="5" class="text-center">No hay pedidos registrados</td></tr>';
                }
            } catch (Exception $e) {
                echo '<tr><td colspan="5" class="text-center text-danger">Error al cargar pedidos: ' . $e->getMessage() . '</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include "footer_layout.php"; ?>