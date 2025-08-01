<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $conexion = new mysqli("localhost", "root", "", "covercars");
  if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
  }

  $nombre = trim($_POST['nombre'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($nombre === '' || $email === '' || $password === '') {
    header("Location: index.php?error=registro_incompleto");
    exit;
  }

  // Verificar si ya existe
  $verif = $conexion->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
  $verif->bind_param("s", $email);
  $verif->execute();
  $verif->store_result();

  if ($verif->num_rows > 0) {
    header("Location: index.php?error=correo_existente");
    exit;
  }

  $passwordHash = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $nombre, $email, $passwordHash);

  if ($stmt->execute()) {
    $_SESSION['usuario'] = [
      'id' => $stmt->insert_id,
      'nombre' => $nombre,
      'email' => $email
    ];
    header("Location: index.php?registro=exito");
    exit;
  } else {
    header("Location: index.php?error=registro_fallido");
    exit;
  }
} else {
  header("Location: index.php");
  exit;
}
?>
