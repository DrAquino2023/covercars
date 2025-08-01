<?php
session_start();
if (!isset($_SESSION['usuario']['id'])) {
    header("Location: login.php");
    exit;
}
include "componentes/header.php";
?>

<div class="container py-5 mt-5 text-center">
  <h1 class="text-danger mb-4">❌ Pago fallido</h1>
  <p class="lead">Lo sentimos, tu pago no se pudo procesar.</p>
  <p>Por favor, intentá nuevamente o contactate con nuestro soporte.</p>

  <div class="my-4">
    <img src="https://cdn-icons-png.flaticon.com/512/753/753345.png" alt="Pago fallido" width="100" />
  </div>

  <a href="checkout.php" class="btn btn-primary">Reintentar pago</a>
  <a href="contacto.php" class="btn btn-outline-secondary">Contactar soporte</a>
</div>

<?php include "componentes/footer.php"; ?>
