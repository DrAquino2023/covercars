document.addEventListener("DOMContentLoaded", () => {
  const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  const resumen = document.getElementById("resumen-pedido");

  if (carrito.length === 0) {
    resumen.innerHTML = "<p>No hay productos en el carrito.</p>";
    return;
  }

  let total = 0;
  let html = `<h5 class="mb-3">Resumen de tu pedido</h5><ul class="list-group mb-3">`;

  carrito.forEach(item => {
    const subtotal = item.precio * item.cantidad;
    total += subtotal;
    html += `
      <li class="list-group-item d-flex justify-content-between align-items-center">
        ${item.nombre} <span>$${subtotal.toLocaleString()}</span>
      </li>
    `;
  });

  html += `</ul><h5>Total estimado: $${total.toLocaleString()}</h5>`;
  resumen.innerHTML = html;

  // Validación del formulario
  const form = document.getElementById("form-checkout");
  form.addEventListener("submit", e => {
    e.preventDefault();
    alert("Gracias por tu compra. Tu pedido será procesado.");
    localStorage.removeItem("carrito");
    window.location.href = "index.php";
  });
});
document.getElementById("btn-pagar").addEventListener("click", function () {
  const carrito = JSON.parse(localStorage.getItem("carrito")) || [];

  fetch("generar_pago.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(carrito)
  })
    .then(res => res.json())
    .then(data => {
      if (data.init_point) {
        window.location.href = data.init_point; // Redirige al checkout de Mercado Pago
      } else {
        alert("No se pudo generar el pago.");
      }
    });
});
