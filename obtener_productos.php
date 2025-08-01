<?php
header("Content-Type: application/json");

$datos = json_decode(file_get_contents("php://input"), true);

if (!isset($datos["ids"]) || !is_array($datos["ids"])) {
    echo json_encode([]);
    exit;
}

$ids = $datos["ids"];

// Verificamos que haya IDs válidos
if (count($ids) === 0) {
    echo json_encode([]);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "covercars");

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Error al conectar con la base de datos"]);
    exit;
}

// Convertimos los IDs en enteros para evitar inyecciones
$ids_limpios = array_map('intval', $ids);
$placeholders = implode(',', array_fill(0, count($ids_limpios), '?'));

$query = "SELECT id, nombre, precio, imagen FROM productos WHERE id IN ($placeholders)";
$stmt = $mysqli->prepare($query);

// Asociamos dinámicamente los parámetros
$tipos = str_repeat('i', count($ids_limpios));
$stmt->bind_param($tipos, ...$ids_limpios);

$stmt->execute();
$resultado = $stmt->get_result();

$productos = [];
while ($fila = $resultado->fetch_assoc()) {
    $productos[$fila["id"]] = $fila;
}

echo json_encode($productos);
