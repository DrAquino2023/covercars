<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email'] ?? '');

  if ($email === '') {
    header("Location: index.php?error=recuperar_vacio");
    exit;
  }

  $conexion = new mysqli("localhost", "root", "", "covercars");
  if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
  }

  $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $resultado = $stmt->get_result();

  if ($resultado->num_rows === 1) {
    // Simulamos el proceso de envío
    // En un entorno real, se debería enviar un mail con un enlace seguro
    header("Location: index.php?recuperar=enviado");
    exit;
  } else {
    header("Location: index.php?error=correo_no_encontrado");
    exit;
  }

} else {
  header("Location: index.php");
  exit;
}
?>
