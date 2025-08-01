<?php
require_once 'conexion.php';
session_start();

$producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "Producto no encontrado.";
    exit;
}

$producto = $resultado->fetch_assoc();
include 'componentes/header.php';
?>

<style>
/* Estilos específicos mejorados para la página de producto */
.producto-container {
    background: var(--white);
    border-radius: var(--border-radius-xl);
    box-shadow: var(--shadow-card);
    overflow: hidden;
    margin-top: 100px;
    margin-bottom: var(--spacing-xl);
    border: 1px solid var(--border-light);
}

.producto-imagen-section {
    position: relative;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: var(--spacing-lg);
}

.producto-imagen-principal {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-card);
    transition: var(--transition-smooth);
    border: 3px solid var(--white);
}

.producto-imagen-principal:hover {
    transform: scale(1.02);
    box-shadow: var(--shadow-elevated);
}

.miniaturas-container {
    display: flex;
    gap: var(--spacing-sm);
    margin-top: var(--spacing-md);
    justify-content: center;
}

.miniatura {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: var(--border-radius-sm);
    cursor: pointer;
    opacity: 0.7;
    transition: var(--transition-smooth);
    border: 2px solid transparent;
    box-shadow: var(--shadow-subtle);
}

.miniatura:hover,
.miniatura.activa {
    opacity: 1;
    border-color: var(--primary-gray);
    transform: scale(1.05);
    box-shadow: var(--shadow-card);
}

.producto-info-section {
    padding: var(--spacing-xl);
    background: var(--white);
}

.producto-titulo {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--primary-gray);
    margin-bottom: var(--spacing-sm);
    line-height: 1.2;
}

.producto-precio {
    font-size: 2.5rem;
    font-weight: 900;
    color: var(--primary-gray);
    margin-bottom: var(--spacing-xl);
    position: relative;
}

.producto-precio::before {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 3px;
    background: var(--primary-gray);
    border-radius: 2px;
}

.personalizacion-wrapper {
    background: linear-gradient(135deg,rgb(253, 254, 255) 0%,rgba(239, 242, 246, 0.41) 100%);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
    border: 1px solid rgb(214, 226, 238);
}

.personalizacion-titulo {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--primary-gray);
    margin-bottom: var(--spacing-lg);
    text-align: center;
    position: relative;
}

.personalizacion-titulo::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 2px;
    background: var(--primary-gray);
    border-radius: 1px;
}

.campo-personalizacion {
    margin-bottom: var(--spacing-lg);
    background: var(--white);
    padding: var(--spacing-md);
    border-radius: var(--border-radius-md);
    box-shadow: var(--shadow-subtle);
    border: 1px solid var(--border-light);
}

.campo-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: var(--primary-gray);
    margin-bottom: var(--spacing-sm);
    font-size: 1rem;
}

.campo-icono {
    width: 24px;
    height: 24px;
    background: var(--primary-gray);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: var(--spacing-xs);
    font-size: 0.8rem;
    color: var(--white);
}

.cantidad-control {
    display: flex;
    align-items: center;
    gap: 0;
    max-width: 140px;
    border-radius: var(--border-radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-subtle);
    border: 1px solid var(--border-light);
}

.cantidad-btn {
    background: var(--primary-gray);
    color: var(--white);
    border: none;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    transition: var(--transition-smooth);
    cursor: pointer;
}

.cantidad-btn:hover {
    background: var(--primary-dark);
}

.cantidad-input {
    border: none;
    text-align: center;
    font-weight: bold;
    font-size: 1.1rem;
    width: 60px;
    height: 40px;
    background: var(--white);
    outline: none;
    color: var(--primary-gray);
}

.tela-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: var(--spacing-xs);
}

.tela-opcion {
    position: relative;
}

.tela-input {
    display: none;
}

.tela-label {
    display: block;
    padding: var(--spacing-sm) var(--spacing-xs);
    border: 2px solid var(--border-light);
    border-radius: var(--border-radius-sm);
    text-align: center;
    font-weight: 600;
    font-size: 0.85rem;
    transition: var(--transition-smooth);
    cursor: pointer;
    background: var(--white);
    color: var(--primary-gray);
}

.tela-input:checked + .tela-label {
    border-color: var(--primary-gray);
    background: var(--primary-gray);
    color: var(--white);
    transform: scale(1.02);
}

.tela-label:hover {
    border-color: var(--primary-gray);
    transform: translateY(-1px);
}

.tamaño-select {
    background: var(--white);
    border: 2px solid var(--border-light);
    border-radius: var(--border-radius-md);
    padding: var(--spacing-sm) var(--spacing-md);
    font-size: 1rem;
    font-weight: 500;
    transition: var(--transition-smooth);
    width: 100%;
    color: var(--primary-gray);
}

.tamaño-select:focus {
    border-color: var(--primary-gray);
    outline: none;
    box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
}

.precio-total-display {
    background: var(--white);
    border: 2px solid var(--primary-gray);
    border-radius: var(--border-radius-md);
    padding: var(--spacing-md);
    text-align: center;
    margin-bottom: var(--spacing-lg);
}

.precio-total-text {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--primary-gray);
}

.acciones-producto {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.btn-agregar-carrito-main {
    background: var(--primary-gray);
    color: var(--white);
    border: none;
    border-radius: var(--border-radius-md);
    padding: var(--spacing-md) var(--spacing-lg);
    font-size: 1.1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: var(--transition-smooth);
}

.btn-agregar-carrito-main:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-card);
}

.btn-agregar-carrito-main.exito {
    background: var(--success-green);
}

.btn-volver-productos {
    background: transparent;
    border: 2px solid var(--medium-gray);
    color: var(--medium-gray);
    border-radius: var(--border-radius-md);
    padding: var(--spacing-sm) var(--spacing-lg);
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: var(--transition-smooth);
}

.btn-volver-productos:hover {
    background: var(--medium-gray);
    color: var(--white);
    text-decoration: none;
}

.caracteristicas-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-sm);
    margin-top: var(--spacing-lg);
}

.caracteristica-item {
    text-align: center;
    padding: var(--spacing-md);
    background: var(--white);
    border-radius: var(--border-radius-md);
    border: 1px solid var(--border-light);
    transition: var(--transition-smooth);
}

.caracteristica-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-card);
}

.caracteristica-icono {
    font-size: 2rem;
    margin-bottom: var(--spacing-xs);
    color: var(--primary-gray);
}

.info-detallada {
    background: var(--white);
    border-radius: var(--border-radius-lg);
    border: 1px solid var(--border-light);
    overflow: hidden;
    margin-top: var(--spacing-xl);
}

.acordeon-item {
    border: none;
    border-bottom: 1px solid var(--border-light);
}

.acordeon-item:last-child {
    border-bottom: none;
}

.acordeon-header-btn {
    background: var(--light-gray);
    color: var(--primary-gray);
    border: none;
    padding: var(--spacing-md) var(--spacing-lg);
    font-weight: 600;
    font-size: 1.1rem;
    transition: var(--transition-smooth);
    width: 100%;
    text-align: left;
}

.acordeon-header-btn:hover,
.acordeon-header-btn:not(.collapsed) {
    background: var(--primary-gray);
    color: var(--white);
}

.acordeon-body {
    padding: var(--spacing-lg);
    background: var(--white);
}

.toast-custom {
    border-radius: var(--border-radius-md);
    border: none;
    box-shadow: var(--shadow-elevated);
    background: var(--primary-gray);
    color: var(--white);
}

@media (max-width: 768px) {
    .producto-container {
        margin-top: 80px;
    }
    
    .producto-titulo {
        font-size: 1.8rem;
    }
    
    .producto-precio {
        font-size: 2rem;
    }
    
    .tela-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .caracteristicas-grid {
        grid-template-columns: 1fr;
    }
    
    .producto-imagen-section,
    .producto-info-section {
        padding: var(--spacing-md);
    }
}
</style>

<main class="container">
    <div class="producto-container fade-in-up">
        <div class="row g-0 personalizacion-wrapper">
            <!-- Columna de imágenes -->
            <div class="col-lg-6">
                <div class="producto-imagen-section">
                    <img src="img/<?= htmlspecialchars($producto['imagen']) ?>" 
                         class="producto-imagen-principal pulse-animation" 
                         alt="<?= htmlspecialchars($producto['nombre']) ?>"
                         id="imagen-principal">
                    
                    <div class="miniaturas-container">
                        <img src="img/<?= htmlspecialchars($producto['imagen']) ?>" 
                             class="miniatura activa" alt="Vista 1"
                             onclick="cambiarImagen('img/<?= htmlspecialchars($producto['imagen']) ?>', this)">
                        <img src="img/<?= htmlspecialchars($producto['imagen']) ?>" 
                             class="miniatura" alt="Vista 2"
                             onclick="cambiarImagen('img/<?= htmlspecialchars($producto['imagen']) ?>', this)">
                        <img src="img/<?= htmlspecialchars($producto['imagen']) ?>" 
                             class="miniatura" alt="Vista 3"
                             onclick="cambiarImagen('img/<?= htmlspecialchars($producto['imagen']) ?>', this)">
                    </div>
                </div>
            </div>

            <!-- Columna de información -->
            <div class="col-lg-6">
                <div class="producto-info-section">
                    <h1 class="producto-titulo"><?= htmlspecialchars($producto['nombre']) ?></h1>
                    <div class="producto-precio">
                        $<?= number_format($producto['precio'], 0, ',', '.') ?>
                    </div>

                    <!-- Personalización mejorada -->
                    <div class="personalizacion-wrapper">
                        <h3 class="personalizacion-titulo">Personalizar Producto</h3>
                        
                        <!-- Cantidad -->
                        <div class="campo-personalizacion">
                            <div class="campo-label">
                                <span class="campo-icono">#</span>
                                Cantidad
                            </div>
                            <div class="cantidad-control">
                                <button class="cantidad-btn" type="button" id="btn-menos-producto">−</button>
                                <input type="number" id="cantidad-producto" class="cantidad-input" min="1" value="1">
                                <button class="cantidad-btn" type="button" id="btn-mas-producto">+</button>
                            </div>
                        </div>

                        <!-- Tipo de tela -->
                        <div class="campo-personalizacion">
                            <div class="campo-label">
                                <span class="campo-icono">⚡</span>
                                Tipo de Tela
                            </div>
                            <div class="tela-grid">
                                <div class="tela-opcion">
                                    <input type="radio" class="tela-input" name="tela" id="tela1" value="Impermeable" checked>
                                    <label class="tela-label" for="tela1">Impermeable</label>
                                </div>
                                <div class="tela-opcion">
                                    <input type="radio" class="tela-input" name="tela" id="tela2" value="Respirable">
                                    <label class="tela-label" for="tela2">Respirable</label>
                                </div>
                                <div class="tela-opcion">
                                    <input type="radio" class="tela-input" name="tela" id="tela3" value="Térmica">
                                    <label class="tela-label" for="tela3">Térmica</label>
                                </div>
                                <div class="tela-opcion">
                                    <input type="radio" class="tela-input" name="tela" id="tela4" value="Algodón">
                                    <label class="tela-label" for="tela4">Algodón</label>
                                </div>
                                <div class="tela-opcion">
                                    <input type="radio" class="tela-input" name="tela" id="tela5" value="Sintética">
                                    <label class="tela-label" for="tela5">Sintética</label>
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">Selecciona el material que mejor se adapte a tus necesidades</small>
                        </div>

                        <!-- Tamaño del vehículo -->
                        <div class="campo-personalizacion">
                            <div class="campo-label">
                                <span class="campo-icono">📐</span>
                                Tamaño del Vehículo
                            </div>
                            <select id="tamaño-producto" class="tamaño-select" required>
                                <option value="">Seleccione el tamaño</option>
                                <option value="XS">XS - Motos y cuatriciclos</option>
                                <option value="S">S - Autos compactos (Gol, Corsa)</option>
                                <option value="M">M - Autos medianos (Corolla, Focus)</option>
                                <option value="L">L - Autos grandes (Camry, Mondeo)</option>
                                <option value="XL">XL - SUVs y camionetas</option>
                                <option value="XXL">XXL - Camiones y vehículos grandes</option>
                            </select>
                        </div>

                        <!-- Total del precio -->
                        <div class="precio-total-display">
                            <div class="precio-total-text">
                                Total: <span id="precio-total-producto">$<?= number_format($producto['precio'], 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="acciones-producto">
                            <button type="button" class="btn-agregar-carrito-main" id="btn-agregar-carrito-producto">
                                Agregar al Carrito
                            </button>
                            <a href="productos.php" class="btn-volver-productos">
                                ← Volver a productos
                            </a>
                        </div>
                    </div>

                    <!-- Características del producto -->
                    <div class="caracteristicas-grid">
                        <div class="caracteristica-item">
                            <div class="caracteristica-icono">🛡️</div>
                            <div>
                                <strong>Garantía</strong><br>
                                <small class="text-muted">12 meses</small>
                            </div>
                        </div>
                        <div class="caracteristica-item">
                            <div class="caracteristica-icono">🚚</div>
                            <div>
                                <strong>Envío</strong><br>
                                <small class="text-muted">Gratis</small>
                            </div>
                        </div>
                        <div class="caracteristica-item">
                            <div class="caracteristica-icono">💬</div>
                            <div>
                                <strong>Soporte</strong><br>
                                <small class="text-muted">24/7</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información detallada -->
        <div class="info-detallada">
            <div class="accordion" id="acordeonProducto">
                <div class="acordeon-item">
                    <h2 class="accordion-header">
                        <button class="acordeon-header-btn accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#descripcion">
                            📋 Descripción del producto
                        </button>
                    </h2>
                    <div id="descripcion" class="accordion-collapse collapse show" data-bs-parent="#acordeonProducto">
                        <div class="acordeon-body">
                            <p class="mb-3"><?= nl2br(htmlspecialchars($producto['descripcion'] ?? 'Funda de alta calidad para protección vehicular. Fabricada con materiales resistentes y duraderos que garantizan la máxima protección para tu vehículo.')) ?></p>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-2">✅ Características principales:</h6>
                                    <ul class="list-unstyled">
                                        <li><span class="text-success me-2">•</span> Resistente al agua y rayos UV</li>
                                        <li><span class="text-success me-2">•</span> Fácil instalación y retiro</li>
                                        <li><span class="text-success me-2">•</span> Materiales de primera calidad</li>
                                        <li><span class="text-success me-2">•</span> Costuras reforzadas</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-2">🎯 Beneficios:</h6>
                                    <ul class="list-unstyled">
                                        <li><span class="text-warning me-2">•</span> Protección 24/7</li>
                                        <li><span class="text-warning me-2">•</span> Mantiene el valor del vehículo</li>
                                        <li><span class="text-warning me-2">•</span> Ahorro en lavados</li>
                                        <li><span class="text-warning me-2">•</span> Diseño elegante</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="acordeon-item">
                    <h2 class="accordion-header">
                        <button class="acordeon-header-btn accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#especificaciones">
                            ⚙️ Especificaciones técnicas
                        </button>
                    </h2>
                    <div id="especificaciones" class="accordion-collapse collapse" data-bs-parent="#acordeonProducto">
                        <div class="acordeon-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Marca:</strong></td>
                                            <td>Covercars</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Material:</strong></td>
                                            <td>Según selección</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Resistencia:</strong></td>
                                            <td>Agua y UV</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Garantía:</strong></td>
                                            <td>12 meses</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Origen:</strong></td>
                                            <td>Argentina</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tiempo de entrega:</strong></td>
                                            <td>5-7 días hábiles</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Instalación:</strong></td>
                                            <td>Incluida</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mantenimiento:</strong></td>
                                            <td>Mínimo</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="acordeon-item">
                    <h2 class="accordion-header">
                        <button class="acordeon-header-btn accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#compartir">
                            📱 Compartir producto
                        </button>
                    </h2>
                    <div id="compartir" class="accordion-collapse collapse" data-bs-parent="#acordeonProducto">
                        <div class="acordeon-body">
                            <p class="mb-3">Comparte este producto con tus amigos y familiares:</p>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <button class="btn btn-primary" onclick="compartirFacebook()">
                                    📘 Facebook
                                </button>
                                <button class="btn btn-info text-white" onclick="compartirTwitter()">
                                    🐦 Twitter
                                </button>
                                <button class="btn btn-success" onclick="compartirWhatsApp()">
                                    📱 WhatsApp
                                </button>
                                <button class="btn btn-secondary" onclick="copiarEnlace()">
                                    🔗 Copiar enlace
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Toast para confirmaciones -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toast-producto" class="toast toast-custom align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-semibold" id="toast-mensaje">
                ✅ Producto agregado al carrito
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>

<?php include 'componentes/footer.php'; ?>

<script>
console.log('🚀 Script de producto mejorado cargado');

document.addEventListener('DOMContentLoaded', function() {
    const producto = {
        id: <?= $producto['id'] ?>,
        nombre: "<?= htmlspecialchars($producto['nombre']) ?>",
        precio: <?= $producto['precio'] ?>,
        imagen: "<?= htmlspecialchars($producto['imagen']) ?>"
    };

    console.log('📦 Producto cargado:', producto);

    const btnMenos = document.getElementById('btn-menos-producto');
    const btnMas = document.getElementById('btn-mas-producto');
    const inputCantidad = document.getElementById('cantidad-producto');
    const selectTamaño = document.getElementById('tamaño-producto');
    const precioTotal = document.getElementById('precio-total-producto');
    const btnAgregar = document.getElementById('btn-agregar-carrito-producto');

    function actualizarPrecioTotal() {
        const cantidad = parseInt(inputCantidad.value) || 1;
        const total = producto.precio * cantidad;
        precioTotal.textContent = `$${total.toLocaleString('es-AR')}`;
    }

    if (btnMenos) {
        btnMenos.addEventListener('click', function() {
            let valor = parseInt(inputCantidad.value) || 1;
            if (valor > 1) {
                inputCantidad.value = valor - 1;
                actualizarPrecioTotal();
            }
        });
    }

    if (btnMas) {
        btnMas.addEventListener('click', function() {
            let valor = parseInt(inputCantidad.value) || 1;
            inputCantidad.value = valor + 1;
            actualizarPrecioTotal();
        });
    }

    if (inputCantidad) {
        inputCantidad.addEventListener('input', actualizarPrecioTotal);
    }

    function agregarProductoAlCarrito(producto) {
        console.log('🛍️ Agregando producto:', producto);
        
        try {
            let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
            
            const existente = carrito.find(item => 
                parseInt(item.id) === parseInt(producto.id) && 
                item.tela === producto.tela && 
                item.tamano === producto.tamano
            );

            if (existente) {
                existente.cantidad += producto.cantidad;
                console.log('➕ Cantidad actualizada');
            } else {
                carrito.push(producto);
                console.log('🆕 Producto agregado');
            }

            localStorage.setItem('carrito', JSON.stringify(carrito));
            actualizarContadorNavbar();
            return true;
        } catch (error) {
            console.error('❌ Error:', error);
            return false;
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

    if (btnAgregar) {
        btnAgregar.addEventListener('click', function() {
            console.log('🖱️ Botón clickeado');
            
            const usuarioLogueado = document.body.dataset.usuario === "1";
            console.log('👤 Usuario logueado:', usuarioLogueado);
            
            if (!usuarioLogueado) {
                console.log('🚪 Mostrando modal de login');
                try {
                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                } catch (error) {
                    alert('Debes iniciar sesión para agregar productos al carrito');
                }
                return;
            }

            const telaSeleccionada = document.querySelector('input[name="tela"]:checked');
            const tamaño = selectTamaño.value;
            const cantidad = parseInt(inputCantidad.value) || 1;

            console.log('📋 Valores:', {
                tela: telaSeleccionada ? telaSeleccionada.value : 'ninguna',
                tamaño: tamaño,
                cantidad: cantidad
            });

            if (!tamaño) {
                alert('Por favor selecciona un tamaño para tu vehículo');
                selectTamaño.focus();
                return;
            }

            if (!telaSeleccionada) {
                alert('Por favor selecciona un tipo de tela');
                return;
            }

            const productoCarrito = {
                id: producto.id,
                nombre: producto.nombre,
                precio: producto.precio,
                imagen: producto.imagen,
                tela: telaSeleccionada.value,
                tamano: tamaño,
                cantidad: cantidad
            };

            const exito = agregarProductoAlCarrito(productoCarrito);
            
            if (exito) {
                const textoOriginal = btnAgregar.innerHTML;
                btnAgregar.innerHTML = '✅ ¡Agregado!';
                btnAgregar.classList.add('exito');
                btnAgregar.disabled = true;
                
                setTimeout(() => {
                    btnAgregar.innerHTML = textoOriginal;
                    btnAgregar.classList.remove('exito');
                    btnAgregar.disabled = false;
                }, 2000);
                
                mostrarToast('✅ Producto agregado al carrito correctamente');
            }
        });
        
        console.log('✅ Event listener del botón configurado');
    } else {
        console.error('❌ No se encontró el botón agregar al carrito');
    }

    function mostrarToast(mensaje) {
        try {
            const toastElement = document.getElementById('toast-producto');
            const toastMensaje = document.getElementById('toast-mensaje');
            
            if (toastElement && toastMensaje) {
                toastMensaje.innerHTML = mensaje;
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            } else {
                alert(mensaje);
            }
        } catch (error) {
            alert(mensaje);
        }
    }

    actualizarContadorNavbar();
    actualizarPrecioTotal();
    
    console.log('✅ Producto.php completamente inicializado');
});

// Funciones auxiliares
function cambiarImagen(src, elemento) {
    const img = document.getElementById('imagen-principal');
    if (img) img.src = src;
    
    document.querySelectorAll('.miniatura').forEach(mini => mini.classList.remove('activa'));
    if (elemento) elemento.classList.add('activa');
}

function compartirFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
}

function compartirTwitter() {
    const url = encodeURIComponent(window.location.href);
    const texto = encodeURIComponent(`Mira esta funda para auto: <?= htmlspecialchars($producto['nombre']) ?>`);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${texto}`, '_blank');
}

function compartirWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const texto = encodeURIComponent(`Mira esta funda para auto: <?= htmlspecialchars($producto['nombre']) ?> - ${url}`);
    window.open(`https://wa.me/?text=${texto}`, '_blank');
}

function copiarEnlace() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('✅ Enlace copiado al portapapeles');
    }, function() {
        alert('❌ No se pudo copiar el enlace');
    });
}
</script>