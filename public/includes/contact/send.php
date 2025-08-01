<?php
// Mostrar errores (desactivar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verificar que el formulario fue enviado correctamente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitización básica
  $nombre = trim(htmlspecialchars($_POST["nombre"] ?? ''));
  $email = trim(htmlspecialchars($_POST["email"] ?? ''));
  $mensaje = trim(htmlspecialchars($_POST["mensaje"] ?? ''));

  // Validaciones del lado del servidor
  if (empty($nombre) || empty($email) || empty($mensaje)) {
    header("Location: index.php#contacto?error=1");
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: index.php#contacto?error=1");
    exit;
  }

  if (strlen($mensaje) > 1000) {
    header("Location: index.php#contacto?error=1");
    exit;
  }

  // Configuración del email
  $destino = "faquino@unlam.edu.ar"; // ← Reemplazar por tu correo real
  $asunto = "Nuevo mensaje de contacto desde Covercars";

  $contenido = "Nombre: $nombre\n";
  $contenido .= "Correo: $email\n";
  $contenido .= "Mensaje:\n$mensaje\n";

  $headers = "From: $email\r\n";
  $headers .= "Reply-To: $email\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

  // Enviar correo
  if (mail($destino, $asunto, $contenido, $headers)) {
    header("Location: index.php?exito=1#contacto");
    exit;
  } else {
    error_log("Error al enviar correo desde Covercars:\n" . print_r($_POST, true));
    header("Location: index.php?error=1#contacto");
    exit;
  }
} else {
  // Si alguien accede directamente al archivo
  http_response_code(403);
  echo "Acceso no autorizado.";
}
?>
