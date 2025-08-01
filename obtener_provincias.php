<?php
// ========================================
// obtener_provincias.php
// Devuelve las provincias según país (JSON)
// ========================================

require_once 'conexion.php';

// Validar parámetro
if (!isset($_GET['pais_id']) || !is_numeric($_GET['pais_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetro inválido']);
    exit;
}

$pais_id = intval($_GET['pais_id']);

$stmt = $conn->prepare("SELECT id, nombre FROM provincias WHERE pais_id = ? ORDER BY nombre ASC");
$stmt->bind_param("i", $pais_id);
$stmt->execute();
$resultado = $stmt->get_result();

$provincias = [];
while ($fila = $resultado->fetch_assoc()) {
    $provincias[] = [
        'id' => $fila['id'],
        'nombre' => $fila['nombre']
    ];
}

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode($provincias);
