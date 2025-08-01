<?php include "../componentes/header.php"; ?>

<?php if (!empty($_GET['db_error'])): ?>
  <div class="modal fade" id="dbErrorModal" tabindex="-1" aria-labelledby="dbErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="dbErrorModalLabel">Error de conexión a la base de datos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
      <div class="modal-body">
        <?= htmlspecialchars($_GET['db_error']) ?>
    </div>
  </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
      var dbErrorModal = new bootstrap.Modal(document.getElementById('dbErrorModal'));
      dbErrorModal.show();
    });
  </script>
  <?php endif; ?>


<!-- HERO -->
<header class="hero-section">
  <div class="container hero-content" data-aos="fade-up">
    <h1 class="display-4">Fundas a medida para tu auto</h1>
    <p class="lead">Protección, estilo y calidad. Más de 20 años de experiencia fabricando fundas únicas.</p>
    <a href="#productos" class="btn btn-warning btn-lg">Ver Productos</a>
  </div>
</header>

<!-- NOSOTROS -->
<section id="nosotros" class="bg-white d-flex align-items-center" style="min-height: 100vh;">
  <div class="container" data-aos="fade-right">
    <div class="row justify-content-center">
      <div class="col-lg-10 text-center">
        <h2 class="mb-4">¿Quiénes somos?</h2>
        <p class="lead">
          En <strong>Covercars</strong> llevamos más de <strong>20 años</strong> dedicados a la fabricación de fundas para autos <strong>a medida</strong>, ofreciendo productos diseñados para proteger lo que más cuidás: tu vehículo.
        </p>
        <p class="fs-5">
          Cada funda que realizamos es única, pensada para adaptarse perfectamente al modelo y estilo de tu auto. Utilizamos materiales de alta calidad, resistentes al agua, al sol y al polvo, garantizando <strong>durabilidad, estilo y protección</strong>.
        </p>
        <blockquote class="blockquote mt-4">
          <p class="fst-italic text-muted">"No vendemos fundas… creamos trajes a medida para tu auto."</p>
        </blockquote>
        <p class="mt-3">
          Fabricamos para autos particulares, empresas, clubes de autos clásicos, concesionarias y más. Si tu vehículo es único, su funda también debe serlo.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- PRODUCTOS -->
<section id="productos" class="bg-light d-flex align-items-center" style="min-height: 100vh;">
  <div class="container">
    <h2 class="text-center mb-5" data-aos="fade-down">Nuestros Productos</h2>
    
    <!-- Grid de productos corregido -->
    <div class="productos-grid">
      <?php if ($productos): while ($p = $productos->fetch_assoc()): ?>
        <div class="producto-card fade-in-up" data-aos="fade-up">
          <!-- Imagen del producto -->
          <div class="producto-imagen-container">
            <img src="img/<?= htmlspecialchars($p['imagen']) ?>" 
                 class="producto-imagen" 
                 alt="<?= htmlspecialchars($p['nombre']) ?>">
            
            <!-- Badge de stock -->
            <span class="badge-stock">En stock</span>
          </div>
          
          <!-- Información del producto -->
          <div class="producto-info">
            <h3 class="producto-nombre"><?= htmlspecialchars($p['nombre']) ?></h3>
            
            <p class="card-text"><?= htmlspecialchars($p['descripcion']) ?></p>
            
            <!-- Características -->
            <div class="producto-caracteristicas mb-3">
              <?php 
              // Agregar badges dinámicos basados en los datos del producto
              if (!empty($p['tipo'])): ?>
                <span class="caracteristica-badge">🏷️ <?= htmlspecialchars($p['tipo']) ?></span>
              <?php endif; ?>
              
              <?php if (!empty($p['color'])): ?>
                <span class="caracteristica-badge">🎨 <?= htmlspecialchars($p['color']) ?></span>
              <?php endif; ?>
              
              <span class="caracteristica-badge">🛡️ Resistente</span>
              <span class="caracteristica-badge">⚡ Personalizable</span>
            </div>
            
            <!-- Precio -->
            <div class="producto-precio">
              $<?= number_format($p['precio'], 0, ',', '.') ?>
            </div>
            
            <!-- Acciones del producto -->
            <div class="producto-acciones">
              <a href="producto.php?id=<?= $p['id'] ?>" class="btn-ver-detalle">
                <i class="bi bi-eye"></i> Ver detalle
              </a>
              
              <button class="agregar-carrito"
                      data-id="<?= $p['id'] ?>"
                      data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                      data-precio="<?= $p['precio'] ?>"
                      data-imagen="<?= htmlspecialchars($p['imagen']) ?>">
                <i class="bi bi-cart-plus"></i> Agregar al carrito
              </button>
            </div>
          </div>
        </div>
      <?php endwhile; else: ?>
          <p class="text-center text-muted">No hay productos disponibles en este momento.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Modal de Personalización -->
<div class="modal fade" id="modalPersonalizar" tabindex="-1" aria-labelledby="modalPersonalizarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title fw-bold" id="modalPersonalizarLabel">
          🎨 Personalizar Producto
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body bg-light">
        <div class="text-center mb-3">
          <h6 class="fw-semibold" id="producto-seleccionado-nombre">Nombre del producto</h6>
          <p class="text-muted mb-0" id="producto-seleccionado-precio">Precio: $0</p>
        </div>
        
        <div class="mb-3">
          <label for="modalTela" class="form-label fw-semibold">🧵 Tipo de tela</label>
          <select id="modalTela" class="form-select">
            <option value="Impermeable">Impermeable - Protección total contra lluvia</option>
            <option value="Respirable">Respirable - Ideal para climas cálidos</option>
            <option value="Térmica">Térmica - Protección contra frío extremo</option>
            <option value="Algodón">Algodón - Suave y natural</option>
            <option value="Sintética">Sintética - Resistente y duradera</option>
          </select>
        </div>
        
        <div class="mb-3">
          <label for="modalTamaño" class="form-label fw-semibold">📏 Tamaño del vehículo</label>
          <select id="modalTamaño" class="form-select">
            <option value="">Seleccionar tamaño</option>
            <option value="XS">XS - Motos y cuatriciclos</option>
            <option value="S">S - Autos compactos (Gol, Corsa)</option>
            <option value="M">M - Autos medianos (Corolla, Focus)</option>
            <option value="L">L - Autos grandes (Camry, Mondeo)</option>
            <option value="XL">XL - SUVs y camionetas</option>
            <option value="XXL">XXL - Camiones y vehículos grandes</option>
          </select>
        </div>
        
        <div class="mb-4">
          <label for="modalCantidad" class="form-label fw-semibold">🔢 Cantidad</label>
          <div class="input-group" style="max-width: 150px;">
            <button class="btn btn-outline-secondary" type="button" id="btn-menos">-</button>
            <input type="number" id="modalCantidad" class="form-control text-center" min="1" value="1">
            <button class="btn btn-outline-secondary" type="button" id="btn-mas">+</button>
          </div>
        </div>
        
        <!-- Resumen del precio -->
        <div class="alert alert-info" role="alert">
          <strong>💰 Total estimado:</strong> 
          <span id="precio-total-modal">$0</span>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          ❌ Cancelar
        </button>
        <button type="button" class="btn btn-warning fw-bold" id="confirmarPersonalizacion">
          🛒 Agregar al carrito
        </button>
      </div>
    </div>
  </div>
</div>

<?php include "../login.php"; ?>
<?php include "../componentes/contacto.php"; ?>
<?php include "../componentes/footer.php"; ?>

<script src="assets/js/personalizar.js"></script>