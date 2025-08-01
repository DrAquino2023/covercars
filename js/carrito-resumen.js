document.addEventListener("DOMContentLoaded", function () {
  const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  const contenedor = document.getElementById("carrito-container");

  if (carrito.length === 0) {
    contenedor.innerHTML = `
        <div class="alert alert-info text-center">
          Tu carrito está vacío.
        </div>
      `;
    return;
  }

  let html = `
      <table class="table table-striped text-center">
        <thead class="table-dark">
          <tr>
            <th>Producto</th>
            <th>Precio unitario</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th>Eliminar</th>
          </tr>
        </thead>
        <tbody>
    `;

  let totalGeneral = 0;

  carrito.forEach((producto, index) => {
    const subtotal = producto.precio * producto.cantidad;
    totalGeneral += subtotal;
    html += `
        <tr>
          <td>${producto.nombre}</td>
          <td>$${producto.precio.toLocaleString()}</td>
          <td>${producto.cantidad}</td>
          <td>$${subtotal.toLocaleString()}</td>
          <td><button class="btn btn-danger btn-sm eliminar-producto" data-index="${index}">🗑️</button></td>
        </tr>
      `;
  });

  html += `
        </tbody>
      </table>
      <div class="text-end fw-bold fs-5">
        Total: $${totalGeneral.toLocaleString()}
      </div>
      <div class="text-end mt-3">
        <a href="index.php" class="btn btn-secondary">← Seguir comprando</a>
      </div>
    `;

  contenedor.innerHTML = html;

  // Listeners para eliminar productos
  document.querySelectorAll(".eliminar-producto").forEach(btn => {
    btn.addEventListener("click", function () {
      const index = this.dataset.index;
      carrito.splice(index, 1);
      localStorage.setItem("carrito", JSON.stringify(carrito));
      location.reload();
    });
  });
});
