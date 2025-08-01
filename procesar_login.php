<?php
// ======================================================
// procesar_login.php - Procesa el inicio de sesión seguro
// ======================================================

// Incluir la configuración de sesión segura
require_once __DIR__ . '/iniciar_sesion_segura.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Conectar a la base de datos
  $conexion = new mysqli("localhost", "root", "", "covercars");
  if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
  }

  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  // Verificar campos obligatorios
  if ($email === '' || $password === '') {
    header("Location: index.php?error=1");
    exit;
  }

  // Buscar usuario por email
  $stmt = $conexion->prepare("SELECT id, nombre, email, password, is_admin FROM usuarios WHERE email = ? LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $resultado = $stmt->get_result();

  if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($password, $usuario['password'])) {
      // Login correcto: guardar datos del usuario en sesión
      $_SESSION['usuario'] = [
        'id' => $usuario['id'],
        'nombre' => $usuario['nombre'],
        'email' => $usuario['email'],
        'is_admin' => (int)$usuario['is_admin']
      ];
      // Agregar id de usuario a la sesión para compatibilidad con otras páginas
      $_SESSION['usuario_id'] = $usuario['id'];

      // Redirigir al editor de perfil tras login exitoso
      header("Location: index.php");
      exit;
    } else {
      // Contraseña incorrecta
      header("Location: index.php?error=2");
      exit;
    }
  } else {
    // Usuario no encontrado
    header("Location: index.php?error=3");
    exit;
  }
} else {
  // Si no se accede vía POST, redirigir al inicio
  header("Location: index.php");
  exit;
}
?>
