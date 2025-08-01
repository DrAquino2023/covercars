<?php
session_start();
if (!isset($_SESSION['usuario']['id'])) {
    header("Location: login.php");
    exit;
}
include "componentes/header.php";
?>

<div class="container py-5 mt-5">
  <h1 class="text-center mb-4">Finalizar compra</h1>

  <div id="resumen-pedido" class="mb-5"></div>

  <div class="text-center">
    <button class="btn btn-primary btn-lg" id="btn-pagar">
      Pagar con Mercado Pago
    </button>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const carrito = JSON.parse(localStorage.getItem("carrito")) || [];

  if (carrito.length === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Tu carrito está vacío',
      text: 'Agregá productos antes de finalizar la compra.',
      confirmButtonText: 'Ir al catálogo'
    }).then(() => window.location.href = 'productos.php');
    return;
  }

  // Construir resumen del pedido
  let total = 0;
  const lista = document.createElement('ul');
  lista.className = 'list-group mb-3';
  carrito.forEach(item => {
    const subtotal = item.precio * item.cantidad;
    total += subtotal;
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.textContent = item.nombre;
    const span = document.createElement('span');
    span.textContent = `$${subtotal.toLocaleString()}`;
    li.appendChild(span);
    lista.appendChild(li);
  });
  const resumenDiv = document.getElementById('resumen-pedido');
  resumenDiv.innerHTML = '<h5 class="mb-3">Resumen del pedido</h5>';
  resumenDiv.appendChild(lista);
  const totalElem = document.createElement('h5');
  totalElem.textContent = `Total: $${total.toLocaleString()}`;
  resumenDiv.appendChild(totalElem);

  // Evento de pago
  document.getElementById('btn-pagar').addEventListener('click', () => {
    fetch("generar_pago.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ carrito })
    })
    .then(res => res.json())
    .then(data => {
      if (data.init_point) {
        window.location.href = data.init_point;
      } else {
        Swal.fire({
          icon: "error",
          title: data.error || "Error al generar la preferencia",
          html: `<pre style="text-align:left; white-space:pre-wrap;">
HTTP ${data.codigo_http || '??'}  
${JSON.stringify(data.respuesta || data, null, 2)}
          </pre>`
        });
      }
    })
    .catch(err => {
      console.error("Error en fetch:", err);
      Swal.fire("Error", "Error de conexión al servidor.", "error");
    });
  });
});
</script>

<?php include "componentes/footer.php"; ?>
