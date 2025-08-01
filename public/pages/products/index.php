<?php
include "componentes/header.php";

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

<style>
/* Estilos mejorados y limpios para la página de productos */
.productos-hero {
  background: var(--primary-black);
  padding: 4rem 0 2rem 0;
  margin-top: 70px;
  color: var(--white);
  position: relative;
  overflow: hidden;
}

.productos-hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(26,26,26,0.8) 100%);
}

.productos-titulo {
  font-size: 3rem;
  font-weight: 800;
  text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
  margin-bottom: 1rem;
  position: relative;
  z-index: 1;
  letter-spacing: -1px;
}

.productos-subtitulo {
  font-size: 1.2rem;
  font-weight: 400;
  opacity: 0.9;
  position: relative;
  z-index: 1;
}

.filtros-container {
  background: var(--white);
  border-radius: var(--border-radius-xl);
  box-shadow: var(--shadow-elevated);
  padding: var(--spacing-xl);
  margin: -3rem auto 3rem auto;
  position: relative;
  z-index: 2;
  max-width: 1200px;
  border: 1px solid var(--border-light);
}

.filtros-titulo {
  color: var(--primary-gray);
  font-weight: 700;
  font-size: 1.3rem;
  margin-bottom: var(--spacing-lg);
  display: flex;
  align-items: center;
  gap: var(--spacing-xs);
}

.filtros-titulo .icono-filtro {
  width: 24px;
  height: 24px;
  background: var(--primary-gray);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--white);
  font-size: 0.9rem;
}

.filtro-grupo {
  margin-bottom: var(--spacing-sm);
}

.filtro-label {
  font-weight: 600;
  color: var(--primary-gray);
  margin-bottom: var(--spacing-xs);
  display: flex;
  align-items: center;
  gap: var(--spacing-xs);
  font-size: 0.95rem;
}

.filtro-icono {
  width: 18px;
  height: 18px;
  background: var(--medium-gray);
  border-radius: 3px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--white);
  font-size: 0.7rem;
  font-weight: bold;
}

.filtro-input, .filtro-select {
  border: 2px solid var(--border-light);
  border-radius: var(--border-radius-md);
  padding: 12px 16px;
  transition: var(--transition-smooth);
  background: var(--white);
  font-size: 0.95rem;
  color: var(--primary-gray);
}

.filtro-input:focus, .filtro-select:focus {
  border-color: var(--primary-gray);
  box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
  outline: none;
  transform: translateY(-1px);
}

.filtro-input::placeholder {
  color: var(--medium-gray);
}

.filtros-botones {
  display: flex;
  gap: var(--spacing-sm);
  align-items: center;
}

.btn-filtrar {
  background: var(--primary-gray);
  border: none;
  border-radius: var(--border-radius-md);
  padding: 12px 24px;
  color: var(--white);
  font-weight: 600;
  transition: var(--transition-smooth);
  display: flex;
  align-items: center;
  gap: var(--spacing-xs);
}

.btn-filtrar:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
  box-shadow: var(--shadow-card);
  color: var(--white);
}

.btn-limpiar {
  background: transparent;
  border: 2px solid var(--medium-gray);
  border-radius: var(--border-radius-md);
  padding: 10px 24px;
  color: var(--medium-gray);
  font-weight: 600;
  transition: var(--transition-smooth);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: var(--spacing-xs);
}

.btn-limpiar:hover {
  background: var(--medium-gray);
  color: var(--white);
  transform: translateY(-2px);
  text-decoration: none;
}

.resultados-info {
  background: var(--primary-gray);
  color: var(--white);
  border-radius: var(--border-radius-md);
  padding: var(--spacing-sm) var(--spacing-lg);
  margin-bottom: var(--spacing-lg);
  text-align: center;
  font-weight: 600;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--spacing-xs);
}

.productos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: var(--spacing-xl);
  margin-bottom: var(--spacing-xl);
}

.producto-card {
  background: var(--white);
  border-radius: var(--border-radius-xl);
  overflow: hidden;
  box-shadow: var(--shadow-card);
  transition: var(--transition-smooth);
  position: relative;
  height: 100%;
  display: flex;
  flex-direction: column;
  border: 1px solid var(--border-light);
}

.producto-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-elevated);
  border-color: rgba(44, 62, 80, 0.2);
}

.producto-imagen-container {
  position: relative;
  overflow: hidden;
  height: 260px;
  background: var(--light-gray);
}

.producto-imagen {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: var(--transition-smooth);
}

.producto-card:hover .producto-imagen {
  transform: scale(1.08);
}

.producto-badge {
  position: absolute;
  top: var(--spacing-sm);
  right: var(--spacing-sm);
  padding: 6px 12px;
  border-radius: var(--border-radius-xl);
  font-size: 0.75rem;
  font-weight: 600;
  z-index: 1;
  backdrop-filter: blur(10px);
}

.badge-stock {
  background: rgba(40, 167, 69, 0.9);
  color: var(--white);
}

.badge-sin-stock {
  background: rgba(220, 53, 69, 0.9);
  color: var(--white);
}

.badge-descuento {
  background: rgba(255, 193, 7, 0.9);
  color: var(--primary-dark);
  top: var(--spacing-sm);
  left: var(--spacing-sm);
  right: auto;
}

.producto-info {
  padding: var(--spacing-lg);
  flex: 1;
  display: flex;
  flex-direction: column;
}

.producto-nombre {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary-gray);
  margin-bottom: var(--spacing-xs);
  line-height: 1.3;
}

.producto-precio {
  font-size: 1.6rem;
  font-weight: 800;
  margin-bottom: var(--spacing-sm);
  color: var(--primary-gray);
}

.precio-descuento {
  color: var(--danger-red);
}

.precio-original {
  text-decoration: line-through;
  color: var(--medium-gray);
  font-size: 1rem;
  margin-right: var(--spacing-xs);
}

.producto-caracteristicas {
  margin-bottom: var(--spacing-sm);
  flex: 1;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.caracteristica-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: var(--light-gray);
  color: var(--primary-gray);
  padding: 4px 10px;
  border-radius: var(--border-radius-xl);
  font-size: 0.75rem;
  font-weight: 600;
  border: 1px solid var(--border-light);
  transition: var(--transition-smooth);
}

.caracteristica-badge:hover {
  background: var(--border-light);
  transform: translateY(-1px);
}

.caracteristica-icono {
  width: 12px;
  height: 12px;
  background: var(--medium-gray);
  border-radius: 2px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--white);
  font-size: 0.6rem;
}

.producto-acciones {
  margin-top: auto;
  display: flex;
  flex-direction: column;
  gap: var(--spacing-xs);
}

.btn-ver-detalle {
  background: transparent;
  border: 2px solid var(--primary-gray);
  color: var(--primary-gray);
  border-radius: var(--border-radius-md);
  padding: var(--spacing-sm) var(--spacing-md);
  font-weight: 600;
  transition: var(--transition-smooth);
  text-decoration: none;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--spacing-xs);
}

.btn-ver-detalle:hover {
  background: var(--primary-gray);
  color: var(--white);
  transform: translateY(-2px);
  text-decoration: none;
}

.btn-agregar-producto {
  background: var(--primary-gray);
  border: none;
  border-radius: var(--border-radius-md);
  padding: var(--spacing-sm) var(--spacing-md);
  color: var(--white);
  font-weight: 700;
  transition: var(--transition-smooth);
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--spacing-xs);
}

.btn-agregar-producto::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s ease;
}

.btn-agregar-producto:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
  box-shadow: var(--shadow-card);
}

.btn-agregar-producto:hover::before {
  left: 100%;
}

.btn-agregar-producto:disabled {
  background: var(--medium-gray);
  color: var(--white);
  cursor: not-allowed;
  opacity: 0.6;
}

.btn-agregar-producto:disabled:hover {
  transform: none;
  box-shadow: none;
}

.paginacion-container {
  background: var(--white);
  border-radius: var(--border-radius-xl);
  padding: var(--spacing-lg);
  box-shadow: var(--shadow-card);
  margin-bottom: var(--spacing-xl);
  border: 1px solid var(--border-light);
}

.pagination .page-link {
  border: 2px solid var(--border-light);
  border-radius: var(--border-radius-sm);
  margin: 0 4px;
  padding: 10px 16px;
  color: var(--primary-gray);
  font-weight: 600;
  transition: var(--transition-smooth);
  background: var(--white);
  text-decoration: none;
}

.pagination .page-link:hover {
  background: var(--primary-gray);
  border-color: var(--primary-gray);
  color: var(--white);
  transform: translateY(-2px);
  text-decoration: none;
}

.pagination .page-item.active .page-link {
  background: var(--primary-gray);
  border-color: var(--primary-gray);
  color: var(--white);
  box-shadow: var(--shadow-card);
}

.pagination .page-item.disabled .page-link {
  opacity: 0.5;
  cursor: not-allowed;
  color: var(--medium-gray);
}

.empty-state {
  text-align: center;
  padding: 80px 20px;
  color: var(--medium-gray);
}

.empty-state-icono {
  font-size: 80px;
  color: var(--border-light);
  margin-bottom: var(--spacing-lg);
  display: block;
}

/* Responsive */
@media (max-width: 768px) {
  .productos-titulo {
    font-size: 2.2rem;
  }
  
  .filtros-container {
    margin: -2rem 1rem 2rem 1rem;
    padding: var(--spacing-lg);
  }
  
  .filtros-botones {
    flex-direction: column;
    width: 100%;
  }
  
  .btn-filtrar, .btn-limpiar {
    width: 100%;
    justify-content: center;
  }
  
  .productos-grid {
    grid-template-columns: 1fr;
    gap: var(--spacing-lg);
  }
}

@media (max-width: 576px) {
  .filtros-container {
    margin: -1rem 0.5rem 1.5rem 0.5rem;
    padding: var(--spacing-md);
  }
  
  .producto-imagen-container {
    height: 220px;
  }
  
  .producto-info {
    padding: var(--spacing-md);
  }
}

/* Animaciones */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

.fade-in-up:nth-child(1) { animation-delay: 0.1s; }
.fade-in-up:nth-child(2) { animation-delay: 0.2s; }
.fade-in-up:nth-child(3) { animation-delay: 0.3s; }
.fade-in-up:nth-child(4) { animation-delay: 0.4s; }
.fade-in-up:nth-child(5) { animation-delay: 0.5s; }
.fade-in-up:nth-child(6) { animation-delay: 0.6s; }
</style>

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