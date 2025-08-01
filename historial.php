<?php
session_start();
if (!isset($_SESSION['usuario']['id'])) {
    header("Location: index.php?error=login_requerido");
    exit;
}
include "componentes/header.php";

$usuario_id = $_SESSION['usuario']['id'];
$mysqli = new mysqli("localhost", "root", "", "covercars");
if ($mysqli->connect_errno) {
    echo "<div class='container py-5 text-center'><h2 class='text-danger'>Error de conexión a la base de datos</h2></div>";
    include "componentes/footer.php";
    exit;
}

// Obtener pedidos del usuario con totales calculados
$queryPedidos = "
    SELECT p.*, 
           COALESCE(SUM(dp.precio_unitario * dp.cantidad), 0) as total_calculado,
           COUNT(dp.id) as total_items
    FROM pedidos p
    LEFT JOIN detalle_pedido dp ON p.id = dp.pedido_id
    WHERE p.usuario_id = ?
    GROUP BY p.id
    ORDER BY p.fecha DESC
";
$stmtPedidos = $mysqli->prepare($queryPedidos);
$stmtPedidos->bind_param("i", $usuario_id);
$stmtPedidos->execute();
$resultPedidos = $stmtPedidos->get_result();
$hay_pedidos = $resultPedidos->num_rows > 0;
?>

<!-- Estilos personalizados -->
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
        border-radius: 0 0 50px 50px;
    }
    
    .hero-section h1 {
        font-weight: 700;
        font-size: 2.5rem;
    }
    
    .pedido-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .pedido-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .pedido-header {
        background: #f8f9fa;
        padding: 20px;
        cursor: pointer;
        border-bottom: 2px solid #e9ecef;
    }
    
    .pedido-header:hover {
        background: #e9ecef;
    }
    
    .pedido-numero {
        font-size: 1.2rem;
        font-weight: 600;
        color: #667eea;
    }
    
    .pedido-fecha {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .pedido-total {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
    }
    
    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .status-approved {
        background: #d4edda;
        color: #155724;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-failure {
        background: #f8d7da;
        color: #721c24;
    }
    
    .detalle-producto {
        border-left: 3px solid #667eea;
        padding-left: 15px;
        margin-bottom: 15px;
    }
    
    .btn-action {
        border-radius: 25px;
        padding: 8px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-recomprar {
        background: #667eea;
        color: white;
        border: none;
    }
    
    .btn-recomprar:hover {
        background: #5a67d8;
        transform: scale(1.05);
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }
    
    .empty-state i {
        font-size: 100px;
        color: #dee2e6;
        margin-bottom: 20px;
    }
    
    .table-productos {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .table-productos thead {
        background: #f8f9fa;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        .pedido-card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3"><i class="bi bi-clock-history me-3"></i>Historial de Compras</h1>
                <p class="lead mb-0">Revisa todos tus pedidos y vuelve a comprar tus productos favoritos</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0 no-print">
                <?php if ($hay_pedidos): ?>
                    <button onclick="window.print()" class="btn btn-light btn-action me-2">
                        <i class="bi bi-printer me-2"></i>Imprimir
                    </button>
                    <button id="exportar-pdf" class="btn btn-light btn-action">
                        <i class="bi bi-filetype-pdf me-2"></i>Exportar PDF
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container py-4" id="pdf-content">
    <?php if ($hay_pedidos): ?>
        <div class="row">
            <div class="col-lg-12">
                <?php while ($pedido = $resultPedidos->fetch_assoc()): 
                    $fecha = date('d/m/Y H:i', strtotime($pedido['fecha']));
                    $total = $pedido['total_calculado'];
                    $status = $pedido['status'] ?? 'pending';
                    $statusText = [
                        'approved' => 'Aprobado',
                        'pending' => 'Pendiente',
                        'failure' => 'Cancelado'
                    ][$status] ?? $status;
                ?>
                    <div class="pedido-card" data-bs-toggle="collapse" data-bs-target="#pedido-<?= $pedido['id'] ?>">
                        <div class="pedido-header">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="pedido-numero">Pedido #<?= $pedido['id'] ?></div>
                                    <div class="pedido-fecha"><?= $fecha ?></div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Estado</small>
                                    <div>
                                        <span class="status-badge status-<?= $status ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Items</small>
                                    <div class="fw-bold"><?= $pedido['total_items'] ?> productos</div>
                                </div>
                                <div class="col-md-3 text-md-end">
                                    <small class="text-muted">Total</small>
                                    <div class="pedido-total">$<?= number_format($total, 2, ',', '.') ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="collapse" id="pedido-<?= $pedido['id'] ?>">
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <i class="bi bi-hash text-muted me-2"></i>
                                                <strong>ID de operación:</strong> 
                                                <code><?= strtoupper(substr(md5($pedido['id'] . '-' . $pedido['fecha']), 0, 16)) ?></code>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <i class="bi bi-calendar-check text-muted me-2"></i>
                                                <strong>Fecha completa:</strong> 
                                                <?= date('d/m/Y \a \l\a\s H:i:s', strtotime($pedido['fecha'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3">Detalle de productos</h5>
                                
                                <?php
                                $stmtDet = $mysqli->prepare("
                                    SELECT p.nombre, p.descripcion, dp.cantidad, dp.precio_unitario
                                    FROM detalle_pedido dp
                                    JOIN productos p ON dp.producto_id = p.id
                                    WHERE dp.pedido_id = ?
                                ");
                                $stmtDet->bind_param("i", $pedido['id']);
                                $stmtDet->execute();
                                $resultDet = $stmtDet->get_result();
                                ?>
                                
                                <div class="row">
                                    <?php while ($producto = $resultDet->fetch_assoc()): 
                                        $subtotal = $producto['precio_unitario'] * $producto['cantidad'];
                                    ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="detalle-producto">
                                                <h6 class="mb-1"><?= htmlspecialchars($producto['nombre']) ?></h6>
                                                <?php if($producto['descripcion']): ?>
                                                    <small class="text-muted d-block mb-2">
                                                        <?= htmlspecialchars(substr($producto['descripcion'], 0, 50)) ?>...
                                                    </small>
                                                <?php endif; ?>
                                                <div class="d-flex justify-content-between">
                                                    <span>
                                                        <strong><?= $producto['cantidad'] ?></strong> x 
                                                        $<?= number_format($producto['precio_unitario'], 2, ',', '.') ?>
                                                    </span>
                                                    <strong class="text-primary">
                                                        $<?= number_format($subtotal, 2, ',', '.') ?>
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile;
                                    $stmtDet->close();
                                    ?>
                                </div>
                                
                                <hr class="my-4">
                                
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <button onclick="verFactura(<?= $pedido['id'] ?>)" class="btn btn-outline-secondary btn-sm me-2 no-print">
                                            <i class="bi bi-file-text me-1"></i> Ver Factura
                                        </button>
                                        <button onclick="contactarSoporte(<?= $pedido['id'] ?>)" class="btn btn-outline-info btn-sm no-print">
                                            <i class="bi bi-headset me-1"></i> Soporte
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <button onclick="recomprar(<?= $pedido['id'] ?>)" class="btn btn-recomprar btn-action no-print">
                                            <i class="bi bi-arrow-repeat me-2"></i>Volver a comprar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
                $stmtPedidos->close();
                ?>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-bag-x"></i>
            <h3 class="text-muted mb-3">Aún no has realizado ninguna compra</h3>
            <p class="text-muted mb-4">Explora nuestro catálogo y encuentra los mejores productos para tu vehículo</p>
            <a href="productos.php" class="btn btn-primary btn-lg btn-action">
                <i class="bi bi-shop me-2"></i>Ver Productos
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function recomprar(pedidoId) {
        window.location.href = 'recomprar.php?pedido=' + pedidoId;
    }
    
    function verFactura(pedidoId) {
        window.open('factura.php?pedido=' + pedidoId, '_blank');
    }
    
    function contactarSoporte(pedidoId) {
        window.location.href = 'contacto.php?ref=pedido-' + pedidoId;
    }

    document.getElementById("exportar-pdf")?.addEventListener("click", () => {
        const element = document.getElementById("pdf-content");
        const opt = {
            margin: 0.5,
            filename: 'historial_compras_covercars.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, letterRendering: true },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        
        html2pdf().set(opt).from(element).save();
    });
</script>

<?php include "componentes/footer.php"; ?>