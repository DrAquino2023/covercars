<?php
require_once "conexion.php"; // Asegurate de tener este archivo con la conexión a la base de datos

// Verificamos si el usuario está logueado
if (!isset($_SESSION['usuario']['id'])) {
  header("Location: index.php");
  exit;
}

$usuario_id = $_SESSION['usuario']['id'];

// Inicializar variables con valores vacíos por defecto
$nombre = "";
$apellido = "";
$telefono = "";
$codigo_pais = "";
$pais_id = "";
$provincia_id = "";
$localidad_id = "";
$domicilio = "";
$observaciones = "";
$documento = "";
$fecha_nacimiento = "";

// Buscar perfil
$sql = "SELECT * FROM perfiles WHERE usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
  $perfil = $resultado->fetch_assoc();
  $apellido = $perfil['apellido'];
  $nombre = $perfil['nombre'];
  $telefono = $perfil['telefono'];
  $codigo_pais = $perfil['codigo_pais'];
  $pais_id = $perfil['pais_id'];
  $provincia_id = $perfil['provincia_id'];
  $localidad_id = $perfil['localidad_id'];
  $domicilio = $perfil['domicilio'];
  $observaciones = $perfil['observaciones'];
  $documento = $perfil['documento'];
  $fecha_nacimiento = $perfil['fecha_nacimiento'];
}
?>
