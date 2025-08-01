

// Funcionalidad del modal de personalización
document.addEventListener('DOMContentLoaded', function() {
  // Botones + y - para cantidad
  const btnMenos = document.getElementById('btn-menos');
  const btnMas = document.getElementById('btn-mas');
  const inputCantidad = document.getElementById('modalCantidad');
  const precioTotalSpan = document.getElementById('precio-total-modal');
  
  let precioUnitario = 0;
  
  // Función para actualizar el precio total
  function actualizarPrecioTotal() {
    const cantidad = parseInt(inputCantidad.value) || 1;
    const total = precioUnitario * cantidad;
    precioTotalSpan.textContent = `$${total.toLocaleString('es-AR')}`;
  }
  
  // Event listeners para cantidad
  btnMenos.addEventListener('click', function() {
    let valor = parseInt(inputCantidad.value) || 1;
    if (valor > 1) {
      inputCantidad.value = valor - 1;
      actualizarPrecioTotal();
    }
  });
  
  btnMas.addEventListener('click', function() {
    let valor = parseInt(inputCantidad.value) || 1;
    inputCantidad.value = valor + 1;
    actualizarPrecioTotal();
  });
  
  inputCantidad.addEventListener('input', actualizarPrecioTotal);
  
  // Actualizar precio cuando se abre el modal
  document.getElementById('modalPersonalizar').addEventListener('show.bs.modal', function() {
    if (window.productoSeleccionado) {
      document.getElementById('producto-seleccionado-nombre').textContent = window.productoSeleccionado.nombre;
      document.getElementById('producto-seleccionado-precio').textContent = `Precio: $${parseFloat(window.productoSeleccionado.precio).toLocaleString('es-AR')}`;
      precioUnitario = parseFloat(window.productoSeleccionado.precio);
      actualizarPrecioTotal();
    }
  });
});
