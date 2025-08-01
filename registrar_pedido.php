<?php
// registrar_pedido.php

// Inicia sesión y verifica autenticación
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'conexion.php';

// Directorio para guardar imágenes
$directorio_subida = __DIR__ . '/uploads/fotos_vehiculos/';

// Crear directorio si no existe
if (!is_dir($directorio_subida)) {
    mkdir($directorio_subida, 0755, true);
}

// Validación del archivo subido
if (!isset($_FILES['foto_vehiculo']) || $_FILES['foto_vehiculo']['error'] !== UPLOAD_ERR_OK) {
    die('Error: Debes adjuntar una foto válida del lateral del vehículo.');
}

// Información del archivo subido
$archivo_nombre = basename($_FILES['foto_vehiculo']['name']);
$extension = strtolower(pathinfo($archivo_nombre, PATHINFO_EXTENSION));

// Extensiones permitidas
$extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
if (!in_array($extension, $extensiones_permitidas)) {
    die('Error: Formato de imagen no permitido. Usa JPG, PNG o WEBP.');
}

// Validación tamaño (máx. 5 MB)
if ($_FILES['foto_vehiculo']['size'] > 5 * 1024 * 1024) {
    die('Error: El archivo excede el tamaño máximo permitido de 5 MB.');
}

// Nuevo nombre del archivo
$nuevo_nombre = 'vehiculo_' . time() . '_' . uniqid() . '.' . $extension;

// Mover el archivo subido a la carpeta destino
$ruta_definitiva = $directorio_subida . $nuevo_nombre;

if (!move_uploaded_file($_FILES['foto_vehiculo']['tmp_name'], $ruta_definitiva)) {
    die('Error al subir el archivo al servidor.');
}

// Registrar pedido en la base de datos
$usuario_id = $_SESSION['usuario_id'];
$fecha = date('Y-m-d H:i:s');
$total = 0;

// Obtener carrito desde sesión o localStorage (aquí lo simulo con sesión)
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    die('Error: Tu carrito está vacío.');
}

$carrito = $_SESSION['carrito'];

foreach ($carrito as $item) {
    $total += ($item['precio'] * $item['cantidad']);
}

// Insertar pedido
$stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, fecha, total) VALUES (?, ?, ?)");
$stmt->bind_param("isd", $usuario_id, $fecha, $total);
$stmt->execute();
$pedido_id = $stmt->insert_id;

// Insertar detalles del pedido
$stmt_detalle = $conn->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario, foto_vehiculo) VALUES (?, ?, ?, ?, ?)");

foreach ($carrito as $item) {
    $stmt_detalle->bind_param(
        "iiids",
        $pedido_id,
        $item['id'],
        $item['cantidad'],
        $item['precio'],
        $nuevo_nombre
    );
    $stmt_detalle->execute();
}

// Limpiar carrito tras confirmar pedido
unset($_SESSION['carrito']);

// Redirigir al usuario tras el pedido
header("Location: pedido_exitoso.php?id=" . $pedido_id);
exit();
