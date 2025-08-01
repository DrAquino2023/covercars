<?php
session_start();
if (!isset($_SESSION['usuario']['id'])) {
    header("Location: login.php");
    exit;
}
include "componentes/header.php";
?>

<div class="container py-5 mt-5 text-center">
  <h1 class="text-warning mb-4">⏳ Pago pendiente</h1>
  <p class="lead">Tu pago está pendiente de confirmación.</p>
  <p>Recibirás un correo con las instrucciones para completar tu compra.</p>

  <div class="my-4">
    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828843.png" alt="Pago pendiente" width="100" />
  </div>

  <a href="historial.php" class="btn btn-primary">Ver historial de compras</a>
  <a href="productos.php" class="btn btn-outline-secondary">Seguir comprando</a>
</div>

<?php include "componentes/footer.php"; ?>
