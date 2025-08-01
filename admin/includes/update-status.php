<?php
session_start();
if (!isset($_SESSION['usuario']['is_admin']) || !$_SESSION['usuario']['is_admin']) {
    http_response_code(403);
    echo json_encode(['error'=>'Acceso denegado']);
    exit;
}
require_once "../conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$pedido_id = $data['pedido_id'] ?? null;
$status = $data['status'] ?? null;

if (!$pedido_id || !$status) {
    http_response_code(400);
    echo json_encode(['error'=>'Datos incompletos']);
    exit;
}

$stmt = $conn->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $pedido_id);
if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
} else {
    http_response_code(500);
    echo json_encode(['error'=>'Error al actualizar estado']);
}
$stmt->close();
?>