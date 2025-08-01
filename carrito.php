<?php include "componentes/header.php"; ?>
<main class="container py-5 mt-5">
  <h1 class="mb-4 fw-bold">🏍️ Carrito de compras</h1>
  <div class="row g-4">
    <!-- Productos -->
    <div class="col-lg-8">
      <div id="carrito-contenido"></div>
      <div id="carrito-vacio" class="text-center text-muted d-none">
        <p class="fs-5">Tu carrito está vacío.</p>
        <a href="productos.php" class="btn btn-outline-primary">Ver productos</a>
      </div>
    </div>

    <!-- Resumen -->
    <div class="col-lg-4">
      <div class="card shadow-sm sticky-top" style="top: 100px;">
        <div class="card-body">
          <h5 class="card-title">Resumen</h5>
          <div id="resumen-detalle" class="small mb-3"></div>
          <p class="mb-2">Total: <strong id="carrito-total">$0,00</strong></p>
          <a href="checkout.php" class="btn btn-success w-100 disabled" id="btn-continuar">Finalizar compra</a>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
function cargarCarrito() {
  const contenedor = document.getElementById('carrito-contenido');
  const vacio = document.getElementById('carrito-vacio');
  const totalEl = document.getElementById('carrito-total');
  const btnContinuar = document.getElementById('btn-continuar');
  const resumenDetalle = document.getElementById('resumen-detalle');

  let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
  contenedor.innerHTML = '';

  let total = 0;
  let detalleResumenHTML = '';

  if (carrito.length === 0) {
    vacio.classList.remove('d-none');
    btnContinuar.classList.add('disabled');
    totalEl.textContent = "$0,00";
    resumenDetalle.innerHTML = '';
    actualizarBadgeCarrito();
    return;
  }

  vacio.classList.add('d-none');
  btnContinuar.classList.remove('disabled');

  carrito.forEach((prod, index) => {
    prod.id = parseInt(prod.id, 10);
    const subtotal = prod.precio * prod.cantidad;
    total += subtotal;

    contenedor.innerHTML += `
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
            <div>
              <h6 class="fw-semibold mb-1">${prod.nombre}</h6>
              <p class="mb-0 text-muted small">Tela: ${prod.tela || 'Sin especificar'} | Tamaño: ${prod.tamano || 'Sin especificar'}</p>
              <p class="mb-0 text-muted small">Precio unitario: $${prod.precio.toLocaleString('es-AR', { minimumFractionDigits: 2 })}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-outline-secondary btn-sm" onclick="cambiarCantidad(${prod.id}, -1)">-</button>
              <input type="number" min="1" id="cantidad-${prod.id}" name="cantidad-${prod.id}" value="${prod.cantidad}" class="form-control form-control-sm text-center" style="width: 60px;" onchange="cambiarCantidadDirecto(${prod.id}, this.value)">
              <button class="btn btn-outline-secondary btn-sm" onclick="cambiarCantidad(${prod.id}, 1)">+</button>
            </div>
            <div class="text-end">
              <p class="mb-0 small">Subtotal:</p>
              <p class="fw-bold mb-0">$${subtotal.toLocaleString('es-AR', { minimumFractionDigits: 2 })}</p>
            </div>
            <button class="btn btn-danger btn-sm" onclick="eliminarProducto(${prod.id})">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>`;

    detalleResumenHTML += `<p class="mb-1">${prod.nombre} (${prod.tela || 'Sin especificar'}, ${prod.tamano || 'Sin especificar'}) x ${prod.cantidad} = $${subtotal.toLocaleString('es-AR', { minimumFractionDigits: 2 })}</p>`;
  });

  totalEl.textContent = `$${total.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`;
  resumenDetalle.innerHTML = detalleResumenHTML;
  actualizarBadgeCarrito();
}

function cambiarCantidad(id, cambio) {
  let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
  const idNum = parseInt(id, 10);
  const prod = carrito.find(p => parseInt(p.id, 10) === idNum);
  if (!prod) return;

  prod.cantidad += cambio;
  if (prod.cantidad <= 0) {
    carrito = carrito.filter(p => parseInt(p.id, 10) !== idNum);
  }

  localStorage.setItem('carrito', JSON.stringify(carrito));
  cargarCarrito();
}

function cambiarCantidadDirecto(id, valor) {
  let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
  const idNum = parseInt(id, 10);
  const nuevaCantidad = parseInt(valor, 10);
  if (isNaN(nuevaCantidad)) return;

  const prod = carrito.find(p => parseInt(p.id, 10) === idNum);
  if (!prod) return;

  if (nuevaCantidad <= 0) {
    carrito = carrito.filter(p => parseInt(p.id, 10) !== idNum);
  } else {
    prod.cantidad = nuevaCantidad;
  }

  localStorage.setItem('carrito', JSON.stringify(carrito));
  cargarCarrito();
}

function eliminarProducto(id) {
  let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
  const idNum = parseInt(id, 10);
  carrito = carrito.filter(p => parseInt(p.id, 10) !== idNum);
  localStorage.setItem('carrito', JSON.stringify(carrito));
  cargarCarrito();
}

function actualizarBadgeCarrito() {
  const cartCountEl = document.getElementById('cart-count');
  if (!cartCountEl) return;

  let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
  let totalItems = 0;
  carrito.forEach(prod => totalItems += prod.cantidad);
  cartCountEl.textContent = totalItems;
}

document.addEventListener('DOMContentLoaded', cargarCarrito);
</script>
<?php include "componentes/footer.php"; ?>