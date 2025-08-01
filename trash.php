<?php
/**
 * Script para mover archivos y carpetas no utilizados a la carpeta "trash"
 */

$trashDir = __DIR__ . '/trash';

// Crear carpeta trash si no existe
if (!file_exists($trashDir)) {
    mkdir($trashDir, 0777, true);
    echo "Carpeta 'trash/' creada.\n";
} else {
    echo "Carpeta 'trash/' ya existe.\n";
}

// Lista de archivos y carpetas a mover
$archivos = [
    'index.php', 'productos.php', 'producto.php', 'carrito.php', 'checkout.php',
    'perfil.php', 'editar_perfil.php', 'historial.php', 'recuperar_contrasena.php',
    'pago_exitoso.php', 'pago_pendiente.php', 'pago_fallido.php',
    'procesar_login.php', 'procesar_registro.php', 'procesar_editar_perfil.php',
    'enviar.php', 'generar_pago.php',

    'migrar.ps1', 'cleanup_covercars.ps1', 'debug_pago.json', 'listado_covercars.txt',

    'js', 'css', 'componentes', 'archivos', '_cleanup_logs'
];

foreach ($archivos as $archivo) {
    $origen = __DIR__ . '/' . $archivo;
    $destino = $trashDir . '/' . basename($archivo);

    if (file_exists($origen)) {
        // Si es carpeta, usar rename directamente
        if (is_dir($origen)) {
            rename($origen, $destino);
            echo "[OK] Carpeta movida: $archivo\n";
        } else {
            rename($origen, $destino);
            echo "[OK] Archivo movido: $archivo\n";
        }
    } else {
        echo "[Aviso] No se encontró: $archivo\n";
    }
}

echo "\nLimpieza completada.\n";
