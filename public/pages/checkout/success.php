<?php
session_start();
if (!isset($_SESSION['usuario']['id'])) {
  header("Location: login.php");
  exit;
}
include "componentes/header.php";
?>

<div class="container py-5 mt-5 text-center">
  <h1 class="text-success mb-4">✅ ¡Gracias por tu compra!</h1>
  <p class="lead">Tu pedido fue procesado con éxito. Te enviaremos un correo con los detalles.</p>

  <div class="my-4">
    <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" alt="Compra exitosa" width="100" />
  </div>

  <a href="productos.php" class="btn btn-primary">Seguir comprando</a>
  <a href="historial.php" class="btn btn-outline-secondary">Ver historial de compras</a>
</div>

<?php include "componentes/footer.php"; ?>
