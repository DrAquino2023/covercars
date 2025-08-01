<?php include "layout.php"; ?>
<?php 
// Manejo de eliminación
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Obtener imagen para eliminarla
    $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
    
    // Eliminar imagen si existe
    if ($producto && $producto['imagen']) {
        $imagen_path = "../uploads/" . $producto['imagen'];
        if (file_exists($imagen_path)) {
            unlink($imagen_path);
        }
    }
    
    // Eliminar producto
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header("Location: productos.php?msg=deleted");
    exit;
}

// Estadísticas
$totalProducts = $conn->query("SELECT COUNT(*) FROM productos")->fetch_row()[0] ?? 0;
$lowStock = $conn->query("SELECT COUNT(*) FROM productos WHERE stock < 10 AND stock > 0")->fetch_row()[0] ?? 0;
$outOfStock = $conn->query("SELECT COUNT(*) FROM productos WHERE stock = 0")->fetch_row()[0] ?? 0;

// Obtener productos
$query = "SELECT id, nombre, precio, stock, imagen, tipo, color, descripcion FROM productos ORDER BY id DESC";
$res = $conn->query($query);
?>

<!-- Header Section -->
<div class="content-header mb-4">
  <div class="row align-items-center">
    <div class="col-sm-6">
      <h1 class="m-0">Gestión de Productos</h1>
      <p class="text-muted mb-0">Administra tu catálogo de productos</p>
    </div>
    <div class="col-sm-6 text-right">
      <a href="producto_form.php" class="btn btn-success">
        <i class="fas fa-plus mr-2"></i> Nuevo Producto
      </a>
    </div>
  </div>
</div>

<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <?php 
  $messages = [
    'created' => 'Producto creado exitosamente',
    'updated' => 'Producto actualizado exitosamente',
    'deleted' => 'Producto eliminado exitosamente'
  ];
  echo $messages[$_GET['msg']] ?? 'Operación realizada exitosamente';
  ?>
  <button type="button" class="close" data-dismiss="alert">
    <span>&times;</span>
  </button>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-primary">
        <i class="fas fa-boxes"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Total Productos</span>
        <span class="info-box-number"><?= $totalProducts ?></span>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-warning">
        <i class="fas fa-exclamation-triangle"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Stock Bajo</span>
        <span class="info-box-number"><?= $lowStock ?></span>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-danger">
        <i class="fas fa-times-circle"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Sin Stock</span>
        <span class="info-box-number"><?= $outOfStock ?></span>
      </div>
    </div>
  </div>
</div>

<!-- Products Table -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Listado de Productos</h3>
  </div>
  <div class="card-body">
    <?php if($res && $res->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-hover" id="tablaProductos">
        <thead>
          <tr>
            <th width="80">Imagen</th>
            <th>Producto</th>
            <th>Tipo</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Estado</th>
            <th width="200" class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while($p = $res->fetch_assoc()): ?>
          <tr>
            <td>
              <?php if($p['imagen'] && file_exists("../uploads/" . $p['imagen'])): ?>
                <img src="../uploads/<?= htmlspecialchars($p['imagen']) ?>" 
                     alt="<?= htmlspecialchars($p['nombre']) ?>" 
                     class="img-thumbnail"
                     style="width: 60px; height: 60px; object-fit: cover;">
              <?php else: ?>
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                     style="width: 60px; height: 60px; border-radius: 4px;">
                  <i class="fas fa-image"></i>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <div>
                <strong><?= htmlspecialchars($p['nombre']) ?></strong>
                <?php if($p['color']): ?>
                  <span class="badge badge-secondary ml-2"><?= htmlspecialchars($p['color']) ?></span>
                <?php endif; ?>
              </div>
              <?php if($p['descripcion']): ?>
                <small class="text-muted d-block mt-1">
                  <?= htmlspecialchars(substr($p['descripcion'], 0, 60)) ?>...
                </small>
              <?php endif; ?>
            </td>
            <td>
              <?= htmlspecialchars($p['tipo'] ?: 'Sin categoría') ?>
            </td>
            <td>
              <strong>$<?= number_format($p['precio'], 2, ',', '.') ?></strong>
            </td>
            <td>
              <?php if($p['stock'] == 0): ?>
                <span class="badge badge-danger">Sin stock</span>
              <?php elseif($p['stock'] < 10): ?>
                <span class="badge badge-warning"><?= $p['stock'] ?> unidades</span>
              <?php else: ?>
                <span class="badge badge-success"><?= $p['stock'] ?> unidades</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if($p['stock'] > 0): ?>
                <span class="badge badge-pill badge-success">
                  <i class="fas fa-check-circle"></i> Disponible
                </span>
              <?php else: ?>
                <span class="badge badge-pill badge-danger">
                  <i class="fas fa-times-circle"></i> Agotado
                </span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <!-- Editar -->
              <a href="producto_form.php?id=<?= $p['id'] ?>" 
                 class="btn btn-primary btn-sm" 
                 title="Editar">
                <i class="fas fa-edit"></i>
              </a>
              
              <!-- Ver detalles -->
              <button type="button" 
                      class="btn btn-info btn-sm" 
                      onclick="verDetalles(<?= $p['id'] ?>, '<?= addslashes($p['nombre']) ?>')"
                      title="Ver detalles">
                <i class="fas fa-eye"></i>
              </button>
              
              <!-- Eliminar -->
              <a href="productos.php?delete=<?= $p['id'] ?>" 
                 class="btn btn-danger btn-sm" 
                 onclick="return confirm('¿Está seguro de eliminar este producto?')"
                 title="Eliminar">
                <i class="fas fa-trash"></i>
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
      <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
      <h5 class="text-muted">No hay productos registrados</h5>
      <p class="text-muted">Comienza agregando tu primer producto</p>
      <a href="producto_form.php" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Agregar Producto
      </a>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal para ver detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Detalles del Producto</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalBody">
        <p>Cargando información...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <a href="#" id="btnEditarModal" class="btn btn-primary">
          <i class="fas fa-edit"></i> Editar
        </a>
      </div>
    </div>
  </div>
</div>

<script>
// Función para ver detalles (funciona sin jQuery)
function verDetalles(id, nombre) {
    // Si jQuery está cargado, usar modal
    if (typeof jQuery !== 'undefined') {
        $('#modalTitle').text('Detalles de ' + nombre);
        $('#modalBody').html(`
            <div class="text-center">
                <p><strong>ID del Producto:</strong> ${id}</p>
                <p><strong>Nombre:</strong> ${nombre}</p>
                <hr>
                <p class="text-info">
                    <i class="fas fa-info-circle"></i> 
                    Para ver todos los detalles, haz clic en Editar.
                </p>
            </div>
        `);
        $('#btnEditarModal').attr('href', 'producto_form.php?id=' + id);
        $('#modalDetalles').modal('show');
    } else {
        // Si jQuery no está cargado, redirigir directamente
        if (confirm('¿Desea ver los detalles de ' + nombre + '?')) {
            window.location.href = 'producto_form.php?id=' + id;
        }
    }
}

// Cuando jQuery esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un momento para jQuery
    setTimeout(function() {
        if (typeof jQuery !== 'undefined') {
            console.log('jQuery está disponible');
            
            // Inicializar DataTable si está disponible
            if ($.fn.DataTable) {
                $('#tablaProductos').DataTable({
                    responsive: true,
                    language: {
                        "sProcessing":     "Procesando...",
                        "sLengthMenu":     "Mostrar _MENU_ registros",
                        "sZeroRecords":    "No se encontraron resultados",
                        "sEmptyTable":     "Ningún dato disponible",
                        "sInfo":           "Mostrando _START_ al _END_ de _TOTAL_ registros",
                        "sInfoEmpty":      "Mostrando 0 al 0 de 0 registros",
                        "sInfoFiltered":   "(filtrado de _MAX_ registros)",
                        "sSearch":         "Buscar:",
                        "oPaginate": {
                            "sFirst":    "Primero",
                            "sLast":     "Último",
                            "sNext":     "Siguiente",
                            "sPrevious": "Anterior"
                        }
                    },
                    order: [[1, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [0, -1] }
                    ]
                });
            }
        } else {
            console.log('jQuery no está disponible aún');
        }
    }, 100);
});
</script>

<?php include "footer_layout.php"; ?>