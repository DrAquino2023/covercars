<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Recibir y limpiar los datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$codigo_pais = trim($_POST['codigo_pais'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$pais = $_POST['pais_id'] ?? '';
$provincia = $_POST['provincia_id'] ?? '';
$localidad = $_POST['localidad_id'] ?? '';
$domicilio = trim($_POST['domicilio'] ?? '');
$observaciones = trim($_POST['observaciones'] ?? '');
$dni = trim($_POST['dni'] ?? '');
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';

// Verificar si ya existe un perfil
$sql_check = "SELECT id FROM perfiles WHERE usuario_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $usuario_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

// Validación previa antes de ejecutar SQL
$pais = (isset($pais) && is_numeric($pais) && intval($pais) > 0) ? intval($pais) : null;
$provincia = (isset($provincia) && is_numeric($provincia) && intval($provincia) > 0) ? intval($provincia) : null;
$localidad = (isset($localidad) && is_numeric($localidad) && intval($localidad) > 0) ? intval($localidad) : null;



if ($result_check && $result_check->num_rows > 0) {
     // Actualizar perfil existente
     $sql_update = "UPDATE perfiles SET 
     nombre = ?, apellido = ?, codigo_pais = ?, telefono = ?, pais_id = ?, 
     provincia_id = ?, localidad_id = ?, domicilio = ?, observaciones = ?, 
     documento = ?, fecha_nacimiento = ?
     WHERE usuario_id = ?";
 $stmt = $conn->prepare($sql_update);
 $stmt->bind_param("ssssiiissssi", 
     $nombre, $apellido, $codigo_pais, $telefono, $pais,
     $provincia, $localidad, $domicilio, $observaciones,
     $dni, $fecha_nacimiento, $usuario_id);
} else {
   // Insertar nuevo perfil
   $sql_insert = "INSERT INTO perfiles (
    usuario_id, nombre, apellido, codigo_pais, telefono, pais_id, 
    provincia_id, localidad_id, domicilio, observaciones, documento, fecha_nacimiento)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("issssiiissss", 
    $usuario_id, $nombre, $apellido, $codigo_pais, $telefono, $pais,
    $provincia, $localidad, $domicilio, $observaciones, $dni, $fecha_nacimiento);
}

if ($stmt->execute()) {
    header("Location: editar_perfil.php?exito=1");
} else {
    echo "Error al guardar los datos del perfil: " . $stmt->error;
}

$stmt->close();
$conn->close();
