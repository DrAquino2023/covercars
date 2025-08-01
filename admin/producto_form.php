<?php 
// NO iniciar sesión aquí porque layout.php ya lo hace
require_once "../conexion.php";

$id = $_GET['id'] ?? null;
$nombre = $descripcion = $precio = $stock = $imagen_actual = $tipo = $color = '';
$caracteristicas = [];
$beneficios = [];
$especificaciones = [];

// Si es edición, cargar datos existentes
if ($id) {
    // Cargar datos básicos del producto
    $stmt = $conn->prepare("SELECT nombre, descripcion, precio, stock, imagen, tipo, color FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nombre, $descripcion, $precio, $stock, $imagen_actual, $tipo, $color);
    $stmt->fetch();
    $stmt->close();
    
    // Cargar características si existen las tablas
    $tabla_existe = $conn->query("SHOW TABLES LIKE 'producto_caracteristicas'")->num_rows > 0;
    if ($tabla_existe) {
        $stmt = $conn->prepare("SELECT caracteristica FROM producto_caracteristicas WHERE producto_id = ? ORDER BY orden");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $caracteristicas[] = $row['caracteristica'];
        }
        $stmt->close();
    }
    
    // Cargar beneficios si existe la tabla
    $tabla_existe = $conn->query("SHOW TABLES LIKE 'producto_beneficios'")->num_rows > 0;
    if ($tabla_existe) {
        $stmt = $conn->prepare("SELECT beneficio FROM producto_beneficios WHERE producto_id = ? ORDER BY orden");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $beneficios[] = $row['beneficio'];
        }
        $stmt->close();
    }
    
    // Cargar especificaciones si existe la tabla
    $tabla_existe = $conn->query("SHOW TABLES LIKE 'producto_especificaciones'")->num_rows > 0;
    if ($tabla_existe) {
        $stmt = $conn->prepare("SELECT clave, valor FROM producto_especificaciones WHERE producto_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $especificaciones[$row['clave']] = $row['valor'];
        }
        $stmt->close();
    }
}

// Procesar formulario ANTES de incluir layout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $tipo = trim($_POST['tipo'] ?? '');
    $color = trim($_POST['color'] ?? '');
    
    // Manejo de imagen
    $imagen = $imagen_actual;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_exts)) {
            $new_filename = 'producto_' . time() . '_' . uniqid() . '.' . $file_ext;
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $new_filename)) {
                if ($imagen_actual && file_exists($upload_dir . $imagen_actual)) {
                    unlink($upload_dir . $imagen_actual);
                }
                $imagen = $new_filename;
            }
        }
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Guardar producto básico
        if ($id) {
            $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, imagen=?, tipo=?, color=? WHERE id=?");
            $stmt->bind_param("ssdisssi", $nombre, $descripcion, $precio, $stock, $imagen, $tipo, $color, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen, tipo, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdisss", $nombre, $descripcion, $precio, $stock, $imagen, $tipo, $color);
        }
        
        $stmt->execute();
        
        if (!$id) {
            $id = $conn->insert_id;
        }
        
        // Verificar si las tablas adicionales existen antes de usarlas
        $tabla_caract = $conn->query("SHOW TABLES LIKE 'producto_caracteristicas'")->num_rows > 0;
        $tabla_benef = $conn->query("SHOW TABLES LIKE 'producto_beneficios'")->num_rows > 0;
        $tabla_espec = $conn->query("SHOW TABLES LIKE 'producto_especificaciones'")->num_rows > 0;
        
        // Guardar características si la tabla existe
        if ($tabla_caract && isset($_POST['caracteristicas'])) {
            $conn->query("DELETE FROM producto_caracteristicas WHERE producto_id = $id");
            $stmt = $conn->prepare("INSERT INTO producto_caracteristicas (producto_id, caracteristica, orden) VALUES (?, ?, ?)");
            $orden = 1;
            foreach ($_POST['caracteristicas'] as $caracteristica) {
                if (!empty(trim($caracteristica))) {
                    $caract = trim($caracteristica);
                    $stmt->bind_param("isi", $id, $caract, $orden);
                    $stmt->execute();
                    $orden++;
                }
            }
        }
        
        // Guardar beneficios si la tabla existe
        if ($tabla_benef && isset($_POST['beneficios'])) {
            $conn->query("DELETE FROM producto_beneficios WHERE producto_id = $id");
            $stmt = $conn->prepare("INSERT INTO producto_beneficios (producto_id, beneficio, orden) VALUES (?, ?, ?)");
            $orden = 1;
            foreach ($_POST['beneficios'] as $beneficio) {
                if (!empty(trim($beneficio))) {
                    $benef = trim($beneficio);
                    $stmt->bind_param("isi", $id, $benef, $orden);
                    $stmt->execute();
                    $orden++;
                }
            }
        }
        
        // Guardar especificaciones si la tabla existe
        if ($tabla_espec && isset($_POST['espec_clave'])) {
            $conn->query("DELETE FROM producto_especificaciones WHERE producto_id = $id");
            $stmt = $conn->prepare("INSERT INTO producto_especificaciones (producto_id, clave, valor) VALUES (?, ?, ?)");
            for ($i = 0; $i < count($_POST['espec_clave']); $i++) {
                if (!empty($_POST['espec_clave'][$i]) && !empty($_POST['espec_valor'][$i])) {
                    $clave = trim($_POST['espec_clave'][$i]);
                    $valor = trim($_POST['espec_valor'][$i]);
                    $stmt->bind_param("iss", $id, $clave, $valor);
                    $stmt->execute();
                }
            }
        }
        
        $conn->commit();
        header("Location: productos.php?msg=" . ($_GET['id'] ? "updated" : "created"));
        exit;
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error al guardar: " . $e->getMessage();
    }
}

// AHORA incluimos el layout
include "layout.php";
?>

<div class="content-header mb-4">
  <div class="row align-items-center">
    <div class="col-sm-6">
      <h1 class="m-0"><?= $id ? "Editar" : "Nuevo" ?> Producto</h1>
    </div>
    <div class="col-sm-6 text-right">
      <a href="productos.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i> Volver
      </a>
    </div>
  </div>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <?= $error ?>
  <button type="button" class="close" data-dismiss="alert">
    <span>&times;</span>
  </button>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <div class="row">
    <!-- Columna principal -->
    <div class="col-lg-8">
      <!-- Información básica -->
      <div class="card mb-3">
        <div class="card-header bg-primary text-white">
          <h3 class="card-title mb-0">
            <i class="fas fa-info-circle mr-2"></i>Información Básica
          </h3>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label>Nombre del Producto <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre) ?>" required>
          </div>
          
          <div class="form-group">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($descripcion) ?></textarea>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Precio (ARS) <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input type="number" step="0.01" name="precio" class="form-control" value="<?= $precio ?>" required>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Stock <span class="text-danger">*</span></label>
                <input type="number" name="stock" class="form-control" value="<?= $stock ?>" min="0" required>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tipo/Categoría</label>
                <select name="tipo" class="form-control">
                  <option value="">Seleccionar...</option>
                  <option value="Funda" <?= $tipo == 'Funda' ? 'selected' : '' ?>>Funda</option>
                  <option value="SUV" <?= $tipo == 'SUV' ? 'selected' : '' ?>>SUV</option>
                  <option value="Sedán" <?= $tipo == 'Sedán' ? 'selected' : '' ?>>Sedán</option>
                  <option value="Personalizada" <?= $tipo == 'Personalizada' ? 'selected' : '' ?>>Personalizada</option>
                  <option value="Accesorios" <?= $tipo == 'Accesorios' ? 'selected' : '' ?>>Accesorios</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Color</label>
                <select name="color" class="form-control">
                  <option value="">Seleccionar...</option>
                  <option value="Negro" <?= $color == 'Negro' ? 'selected' : '' ?>>Negro</option>
                  <option value="Gris" <?= $color == 'Gris' ? 'selected' : '' ?>>Gris</option>
                  <option value="Azul" <?= $color == 'Azul' ? 'selected' : '' ?>>Azul</option>
                  <option value="Rojo" <?= $color == 'Rojo' ? 'selected' : '' ?>>Rojo</option>
                  <option value="Plata" <?= $color == 'Plata' ? 'selected' : '' ?>>Plata</option>
                  <option value="Personalizado" <?= $color == 'Personalizado' ? 'selected' : '' ?>>Personalizado</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Características principales -->
      <div class="card mb-3">
        <div class="card-header bg-success text-white">
          <h3 class="card-title mb-0">
            <i class="fas fa-check-circle mr-2"></i>Características Principales
          </h3>
        </div>
        <div class="card-body">
          <div id="caracteristicas-container">
            <?php 
            // Características por defecto si no hay ninguna
            if (empty($caracteristicas)) {
              $caracteristicas = [
                'Resistente al agua y rayos UV',
                'Fácil instalación y retiro',
                'Materiales de primera calidad',
                'Costuras reforzadas'
              ];
            }
            foreach ($caracteristicas as $caracteristica): ?>
            <div class="input-group mb-2">
              <div class="input-group-prepend">
                <span class="input-group-text bg-success text-white">
                  <i class="fas fa-check"></i>
                </span>
              </div>
              <input type="text" name="caracteristicas[]" class="form-control" 
                     value="<?= htmlspecialchars($caracteristica) ?>" 
                     placeholder="Ingrese una característica">
              <div class="input-group-append">
                <button type="button" class="btn btn-danger btn-remove-item">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-sm btn-success" id="add-caracteristica">
            <i class="fas fa-plus mr-1"></i> Agregar característica
          </button>
        </div>
      </div>
      
      <!-- Beneficios -->
      <div class="card mb-3">
        <div class="card-header bg-warning">
          <h3 class="card-title mb-0">
            <i class="fas fa-star mr-2"></i>Beneficios
          </h3>
        </div>
        <div class="card-body">
          <div id="beneficios-container">
            <?php 
            if (empty($beneficios)) {
              $beneficios = [
                'Protección 24/7',
                'Mantiene el valor del vehículo',
                'Ahorro en lavados',
                'Diseño elegante'
              ];
            }
            foreach ($beneficios as $beneficio): ?>
            <div class="input-group mb-2">
              <div class="input-group-prepend">
                <span class="input-group-text bg-warning">
                  <i class="fas fa-star"></i>
                </span>
              </div>
              <input type="text" name="beneficios[]" class="form-control" 
                     value="<?= htmlspecialchars($beneficio) ?>" 
                     placeholder="Ingrese un beneficio">
              <div class="input-group-append">
                <button type="button" class="btn btn-danger btn-remove-item">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-sm btn-warning" id="add-beneficio">
            <i class="fas fa-plus mr-1"></i> Agregar beneficio
          </button>
        </div>
      </div>
      
      <!-- Especificaciones técnicas -->
      <div class="card mb-3">
        <div class="card-header bg-info text-white">
          <h3 class="card-title mb-0">
            <i class="fas fa-cog mr-2"></i>Especificaciones Técnicas
          </h3>
        </div>
        <div class="card-body">
          <div id="especificaciones-container">
            <?php 
            $especificaciones_default = [
              'Marca' => $especificaciones['Marca'] ?? 'Covercars',
              'Material' => $especificaciones['Material'] ?? 'Según selección',
              'Resistencia' => $especificaciones['Resistencia'] ?? 'Agua y UV',
              'Garantía' => $especificaciones['Garantía'] ?? '12 meses',
              'Origen' => $especificaciones['Origen'] ?? 'Argentina',
              'Tiempo de entrega' => $especificaciones['Tiempo de entrega'] ?? '5-7 días hábiles',
              'Instalación' => $especificaciones['Instalación'] ?? 'Incluida',
              'Mantenimiento' => $especificaciones['Mantenimiento'] ?? 'Mínimo'
            ];
            
            foreach ($especificaciones_default as $clave => $valor): ?>
            <div class="row mb-2">
              <div class="col-md-4">
                <input type="text" name="espec_clave[]" class="form-control" 
                       value="<?= htmlspecialchars($clave) ?>" placeholder="Característica">
              </div>
              <div class="col-md-7">
                <input type="text" name="espec_valor[]" class="form-control" 
                       value="<?= htmlspecialchars($valor) ?>" placeholder="Valor">
              </div>
              <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-remove-spec">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-sm btn-info text-white" id="add-especificacion">
            <i class="fas fa-plus mr-1"></i> Agregar especificación
          </button>
        </div>
      </div>
    </div>
    
    <!-- Columna lateral -->
    <div class="col-lg-4">
      <!-- Imagen del producto -->
      <div class="card mb-3">
        <div class="card-header">
          <h3 class="card-title">Imagen del Producto</h3>
        </div>
        <div class="card-body text-center">
          <?php if($imagen_actual && file_exists("../uploads/" . $imagen_actual)): ?>
            <img src="../uploads/<?= htmlspecialchars($imagen_actual) ?>" 
                 alt="Imagen actual" 
                 class="img-fluid mb-3"
                 style="max-height: 200px; border-radius: 8px;">
            <p class="text-muted">Imagen actual</p>
          <?php else: ?>
            <div class="bg-light p-5 mb-3" style="border-radius: 8px;">
              <i class="fas fa-image fa-4x text-muted"></i>
              <p class="text-muted mt-2">Sin imagen</p>
            </div>
          <?php endif; ?>
          
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/*">
            <label class="custom-file-label" for="imagen">Elegir imagen</label>
          </div>
          <small class="form-text text-muted">
            JPG, PNG, GIF (Máx: 5MB)
          </small>
        </div>
      </div>
      
      <!-- Acciones -->
      <div class="card">
        <div class="card-body">
          <button type="submit" class="btn btn-primary btn-block btn-lg">
            <i class="fas fa-save mr-2"></i>
            <?= $id ? "Actualizar" : "Guardar" ?> Producto
          </button>
          <a href="productos.php" class="btn btn-secondary btn-block">
            Cancelar
          </a>
          <?php if($id): ?>
          <hr>
          <a href="productos.php?delete=<?= $id ?>" 
             class="btn btn-danger btn-block"
             onclick="return confirm('¿Está seguro de eliminar este producto?')">
            <i class="fas fa-trash mr-2"></i> Eliminar
          </a>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Ayuda -->
      <div class="card mt-3">
        <div class="card-body bg-light">
          <h6 class="text-primary mb-2">
            <i class="fas fa-question-circle mr-1"></i> Ayuda
          </h6>
          <small>
            <ul class="mb-0 pl-3">
              <li>Las características y beneficios aparecerán en la página del producto</li>
              <li>Las especificaciones se mostrarán en una tabla</li>
              <li>Puedes agregar o quitar campos según necesites</li>
            </ul>
          </small>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
// Mostrar nombre del archivo seleccionado
document.getElementById('imagen').addEventListener('change', function(e) {
    var fileName = e.target.files[0]?.name || 'Elegir imagen';
    e.target.nextElementSibling.innerText = fileName;
});

// Agregar característica
document.getElementById('add-caracteristica').addEventListener('click', function() {
    var container = document.getElementById('caracteristicas-container');
    var newItem = document.createElement('div');
    newItem.className = 'input-group mb-2';
    newItem.innerHTML = `
        <div class="input-group-prepend">
            <span class="input-group-text bg-success text-white"><i class="fas fa-check"></i></span>
        </div>
        <input type="text" name="caracteristicas[]" class="form-control" placeholder="Nueva característica">
        <div class="input-group-append">
            <button type="button" class="btn btn-danger btn-remove-item">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newItem);
});

// Agregar beneficio
document.getElementById('add-beneficio').addEventListener('click', function() {
    var container = document.getElementById('beneficios-container');
    var newItem = document.createElement('div');
    newItem.className = 'input-group mb-2';
    newItem.innerHTML = `
        <div class="input-group-prepend">
            <span class="input-group-text bg-warning"><i class="fas fa-star"></i></span>
        </div>
        <input type="text" name="beneficios[]" class="form-control" placeholder="Nuevo beneficio">
        <div class="input-group-append">
            <button type="button" class="btn btn-danger btn-remove-item">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newItem);
});

// Agregar especificación
document.getElementById('add-especificacion').addEventListener('click', function() {
    var container = document.getElementById('especificaciones-container');
    var newRow = document.createElement('div');
    newRow.className = 'row mb-2';
    newRow.innerHTML = `
        <div class="col-md-4">
            <input type="text" name="espec_clave[]" class="form-control" placeholder="Característica">
        </div>
        <div class="col-md-7">
            <input type="text" name="espec_valor[]" class="form-control" placeholder="Valor">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-remove-spec">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
});

// Eliminar items
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove-item') || e.target.parentElement.classList.contains('btn-remove-item')) {
        e.target.closest('.input-group').remove();
    }
    if (e.target.classList.contains('btn-remove-spec') || e.target.parentElement.classList.contains('btn-remove-spec')) {
        e.target.closest('.row').remove();
    }
});
</script>

<?php include "footer_layout.php"; ?>