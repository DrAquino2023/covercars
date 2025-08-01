<?php
include "../componentes/header.php";

$mysqli = new mysqli("localhost", "root", "", "covercars");
if ($mysqli->connect_errno) {
  echo "<div class='container py-5 text-center'><h2 class='text-danger'>Error de conexión.</h2></div>";
  include "componentes/footer.php";
  exit;
}

// Filtros
$precio_min = isset($_GET['precio_min']) ? (float) $_GET['precio_min'] : 0;
$precio_max = isset($_GET['precio_max']) ? (float) $_GET['precio_max'] : 1000000;
$tipo = isset($_GET['tipo']) ? $mysqli->real_escape_string($_GET['tipo']) : '';
$color = isset($_GET['color']) ? $mysqli->real_escape_string($_GET['color']) : '';

// Paginación
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$por_pagina = 6;
$offset = ($pagina - 1) * $por_pagina;

$condiciones = ["precio BETWEEN $precio_min AND $precio_max"];
if ($tipo !== '') $condiciones[] = "tipo = '$tipo'";
if ($color !== '') $condiciones[] = "color = '$color'";

$where = count($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '';

$total_query = $mysqli->query("SELECT COUNT(*) AS total FROM productos $where");
$total_resultado = $total_query->fetch_assoc();
$total_productos = $total_resultado['total'];
$total_paginas = ceil($total_productos / $por_pagina);

$productos = $mysqli->query("SELECT * FROM productos $where ORDER BY nombre ASC LIMIT $por_pagina OFFSET $offset");

$tipos = $mysqli->query("SELECT DISTINCT tipo FROM productos WHERE tipo IS NOT NULL AND tipo != ''");
$colores = $mysqli->query("SELECT DISTINCT color FROM productos WHERE color IS NOT NULL AND color != ''");

function build_query($overrides = []) {
  $params = array_merge($_GET, $overrides);
  return http_build_query($params);
}
?>

<main>
  <!-- Hero Section -->
  <section class="productos-hero">
    <div class="container text-center">
      <h1 class="productos-titulo">Catálogo de Productos</h1>
      <p class="productos-subtitulo">Encuentra la funda perfecta para tu vehículo</p>
    </div>
  </section>

  <div class="container">
    <!-- Filtros mejorados -->
    <div class="filtros-container">
      <h3 class="filtros-titulo">
        <span class="icono-filtro">⚡</span>
        Filtrar productos
      </h3>
      
      <form method="get" class="row g-3">
        <div class="col-md-2">
          <div class="filtro-grupo">
            <label class="filtro-label">
              <span class="filtro-icono">$</span>
              Precio desde
            </label>
            <input type="number" name="precio_min" class="filtro-input form-control" 
                   value="<?= $precio_min ?>" placeholder="Precio mínimo">
          </div>
        </div>
        
        <div class="col-md-2">
          <div class="filtro-grupo">
            <label class="filtro-label">
              <span class="filtro-icono">$</span>
              Precio hasta
            </label>
            <input type="number" name="precio_max" class="filtro-input form-control" 
                   value="<?= $precio_max ?>" placeholder="Precio máximo">
          </div>
        </div>
        
        <div class="col-md-2">
          <div class="filtro-grupo">
            <label class="filtro-label">
              <span class="filtro-icono">T</span>
              Tipo
            </label>
            <select name="tipo" class="filtro-select form-select">
              <option value="">Todos los tipos</option>
              <?php while ($t = $tipos->fetch_assoc()): ?>
                <option value="<?= $t['tipo'] ?>" <?= $tipo == $t['tipo'] ? 'selected' : '' ?>>
                  <?= $t['tipo'] ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>
        
        <div class="col-md-2">
          <div class="filtro-grupo">
            <label class="filtro-label">
              <span class="filtro-icono">C</span>
              Color
            </label>
            <select name="color" class="filtro-select form-select">
              <option value="">Todos los colores</option>
              <?php while ($c = $colores->fetch_assoc()): ?>
                <option value="<?= $c['color'] ?>" <?= $color == $c['color'] ? 'selected' : '' ?>>
                  <?= $c['color'] ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>
        
        <div class="col-md-2">
          <div class="filtro-grupo">
            <label class="filtro-label">&nbsp;</label>
            <div class="filtros-botones">
              <button type="submit" class="btn btn-filtrar">
                <i class="bi bi-search"></i>
                Filtrar
              </button>
              <a href="productos.php" class="btn btn-limpiar">
                <i class="bi bi-x-circle"></i>
                Limpiar
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Información de resultados -->
    <?php if ($total_productos > 0): ?>
      <div class="resultados-info">
        <i class="bi bi-info-circle"></i>
        Mostrando <?= min($por_pagina, $total_productos - $offset) ?> de <?= $total_productos ?> productos encontrados
      </div>
    <?php endif; ?>

    <!-- Grid de productos -->
    <div class="productos-grid">
      <?php while ($p = $productos->fetch_assoc()): ?>
        <div class="producto-card fade-in-up">
          <div class="producto-imagen-container">
            <img src="img/<?= htmlspecialchars($p['imagen']) ?>" 
                 class="producto-imagen" 
                 alt="<?= htmlspecialchars($p['nombre']) ?>">
            
            <!-- Badges -->
            <?php if (!empty($p['descuento']) && $p['descuento'] > 0): ?>
              <span class="producto-badge badge-descuento">-<?= $p['descuento'] ?>%</span>
            <?php endif; ?>
            
            <span class="producto-badge <?= (isset($p['stock']) && $p['stock'] > 0) ? 'badge-stock' : 'badge-sin-stock' ?>">
              <?= (isset($p['stock']) && $p['stock'] > 0) ? 'En stock' : 'Sin stock' ?>
            </span>
          </div>
          
          <div class="producto-info">
            <h3 class="producto-nombre"><?= htmlspecialchars($p['nombre']) ?></h3>
            
            <div class="producto-precio">
              <?php if (!empty($p['descuento']) && $p['descuento'] > 0): 
                $precio_desc = $p['precio'] * (1 - $p['descuento'] / 100);
              ?>
                <span class="precio-original">$<?= number_format($p['precio'], 0, ',', '.') ?></span>
                <span class="precio-descuento">$<?= number_format($precio_desc, 0, ',', '.') ?></span>
              <?php else: ?>
                $<?= number_format($p['precio'], 0, ',', '.') ?>
              <?php endif; ?>
            </div>
            
            <div class="producto-caracteristicas">
              <?php if (!empty($p['personalizable'])): ?>
                <span class="caracteristica-badge">
                  <span class="caracteristica-icono">P</span>
                  Personalizable
                </span>
              <?php endif; ?>
              <?php if (!empty($p['tipo'])): ?>
                <span class="caracteristica-badge">
                  <span class="caracteristica-icono">T</span>
                  <?= htmlspecialchars($p['tipo']) ?>
                </span>
              <?php endif; ?>
              <?php if (!empty($p['color'])): ?>
                <span class="caracteristica-badge">
                  <span class="caracteristica-icono">C</span>
                  <?= htmlspecialchars($p['color']) ?>
                </span>
              <?php endif; ?>
            </div>
            
            <div class="producto-acciones">
              <a href="producto.php?id=<?= $p['id'] ?>" class="btn-ver-detalle">
                <i class="bi bi-eye"></i> 
                Ver detalle
              </a>
              <button class="btn-agregar-producto" 
                      onclick='agregarAlCarrito(<?= json_encode($p) ?>)' 
                      <?= (isset($p['stock']) && $p['stock'] > 0) ? '' : 'disabled' ?>>
                <i class="bi bi-cart-plus"></i>
                <?= (isset($p['stock']) && $p['stock'] > 0) ? 'Agregar al carrito' : 'Sin stock' ?>
              </button>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <!-- Paginación -->
    <?php if ($total_paginas > 1): ?>
      <div class="paginacion-container">
        <nav>
          <ul class="pagination justify-content-center mb-0">
            <li class="page-item <?= $pagina == 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?<?= build_query(['pagina' => $pagina - 1]) ?>">
                <i class="bi bi-chevron-left"></i> Anterior
              </a>
            </li>
            
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
              <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                <a class="page-link" href="?<?= build_query(['pagina' => $i]) ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            
            <li class="page-item <?= $pagina == $total_paginas ? 'disabled' : '' ?>">
              <a class="page-link" href="?<?= build_query(['pagina' => $pagina + 1]) ?>">
                Siguiente <i class="bi bi-chevron-right"></i>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    <?php endif; ?>

    <!-- Mensaje si no hay productos -->
    <?php if ($total_productos == 0): ?>
      <div class="empty-state">
        <span class="empty-state-icono">🔍</span>
        <h3>No se encontraron productos</h3>
        <p>Intenta ajustar los filtros de búsqueda</p>
        <a href="productos.php" class="btn btn-primary">Ver todos los productos</a>
      </div>
    <?php endif; ?>
  </div>
</main>

<!-- Toast para confirmaciones -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
  <div id="toast-productos" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body fw-semibold" id="toast-productos-mensaje">
        <i class="bi bi-check-circle me-2"></i> Producto agregado al carrito
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>
  </div>
</div>

<script>
console.log('🚀 Script de productos mejorado cargado');

function agregarAlCarrito(producto) {
  try {
    const usuarioLogueado = document.body.dataset.usuario === "1";
    
    if (!usuarioLogueado) {
      console.log('🚪 Usuario no logueado, mostrando modal');
      try {
        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
      } catch (error) {
        alert('Debes iniciar sesión para agregar productos al carrito');
      }
      return;
    }

    console.log('🛍️ Agregando producto desde catálogo:', producto);

    const productoCarrito = {
      id: parseInt(producto.id) || 0,
      nombre: producto.nombre || 'Producto sin nombre',
      precio: parseFloat(producto.precio) || 0,
      imagen: producto.imagen || 'default.jpg',
      tela: 'Sin especificar',
      tamano: 'Sin especificar',
      cantidad: 1
    };

    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    
    const existente = carrito.find(item => 
      parseInt(item.id) === parseInt(productoCarrito.id) && 
      item.tela === productoCarrito.tela && 
      item.tamano === productoCarrito.tamano
    );

    if (existente) {
      existente.cantidad += 1;
      console.log('➕ Cantidad actualizada');
    } else {
      carrito.push(productoCarrito);
      console.log('🆕 Producto nuevo agregado');
    }

    localStorage.setItem('carrito', JSON.stringify(carrito));
    actualizarContadorNavbar();
    mostrarToast(`"${producto.nombre}" agregado al carrito`);
    
  } catch (error) {
    console.error('❌ Error agregando producto:', error);
    alert('Error al agregar el producto al carrito');
  }
}

function actualizarContadorNavbar() {
  try {
    const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    const total = carrito.reduce((acc, p) => acc + (p.cantidad || 0), 0);
    const badge = document.getElementById('contador-carrito');
    if (badge) {
      badge.textContent = total;
      console.log('🔢 Contador actualizado:', total);
    }
  } catch (error) {
    console.error('❌ Error actualizando contador:', error);
  }
}

function mostrarToast(mensaje) {
  try {
    const toastElement = document.getElementById('toast-productos');
    const toastMensaje = document.getElementById('toast-productos-mensaje');
    
    if (toastElement && toastMensaje) {
      toastMensaje.innerHTML = `<i class="bi bi-check-circle me-2"></i> ${mensaje}`;
      const toast = new bootstrap.Toast(toastElement);
      toast.show();
    } else {
      console.log('📢 Toast no encontrado, usando alert');
      alert(mensaje);
    }
  } catch (error) {
    console.error('❌ Error mostrando toast:', error);
    alert(mensaje);
  }
}

// Inicializar contador al cargar la página
document.addEventListener('DOMContentLoaded', function() {
  actualizarContadorNavbar();
  console.log('✅ Productos.php inicializado correctamente');
});
</script>

<?php include "componentes/footer.php"; ?>