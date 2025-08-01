// =============================================
// CARRITO.JS - Sistema completo de carrito (SIN ERRORES)
// =============================================

// Variable global para el producto seleccionado
let productoSeleccionado = null;

// Inicialización SEGURA al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Carrito.js iniciado correctamente');
    
    // Verificar que todo esté disponible antes de proceder
    if (typeof Storage === "undefined") {
        console.error('❌ LocalStorage no disponible');
        return;
    }
    
    try {
        actualizarContador();
        configurarEventosCarrito();
    } catch (error) {
        console.error('❌ Error en inicialización:', error);
    }
});

// =============================================
// CONFIGURACIÓN SEGURA DE EVENTOS
// =============================================
function configurarEventosCarrito() {
    try {
        // Botones "Agregar al carrito" en páginas principales
        const botonesAgregar = document.querySelectorAll('.agregar-carrito');
        if (botonesAgregar.length > 0) {
            botonesAgregar.forEach(boton => {
                if (boton && typeof boton.addEventListener === 'function') {
                    boton.addEventListener('click', function(e) {
                        e.preventDefault();
                        manejarAgregarCarrito(this);
                    });
                }
            });
            console.log(`✅ ${botonesAgregar.length} botones agregar configurados`);
        }

        // Botón confirmar personalización en modal
        const btnConfirmar = document.getElementById('confirmarPersonalizacion');
        if (btnConfirmar && typeof btnConfirmar.addEventListener === 'function') {
            btnConfirmar.addEventListener('click', confirmarPersonalizacion);
            console.log('✅ Botón confirmar personalización configurado');
        }

        // Si estamos en la página del carrito, cargar productos
        const carritoContenido = document.getElementById('carrito-contenido');
        if (carritoContenido) {
            console.log('📦 Detectada página de carrito, cargando...');
            cargarCarrito();
        }
        
    } catch (error) {
        console.error('❌ Error configurando eventos:', error);
    }
}

// =============================================
// MANEJAR AGREGAR AL CARRITO (SEGURO)
// =============================================
function manejarAgregarCarrito(boton) {
    try {
        // Verificar elemento válido
        if (!boton || !boton.dataset) {
            console.error('❌ Botón inválido');
            return;
        }

        // Verificar si el usuario está logueado
        const bodyElement = document.body;
        const usuarioLogueado = bodyElement && bodyElement.dataset && bodyElement.dataset.usuario === "1";
        
        if (!usuarioLogueado) {
            console.log('🚪 Usuario no logueado, mostrando modal');
            mostrarModalLogin();
            return;
        }

        // Guardar producto seleccionado
        productoSeleccionado = {
            id: parseInt(boton.dataset.id) || 0,
            nombre: boton.dataset.nombre || 'Producto sin nombre',
            precio: parseFloat(boton.dataset.precio) || 0,
            imagen: boton.dataset.imagen || "default.jpg"
        };

        console.log('📋 Producto seleccionado:', productoSeleccionado);

        // Mostrar modal de personalización
        const modalElement = document.getElementById('modalPersonalizar');
        if (modalElement && typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            console.warn('⚠️ Modal de personalización no encontrado, agregando directamente');
            // Agregar directamente sin personalización
            agregarProductoAlCarrito({
                ...productoSeleccionado,
                tela: 'Sin especificar',
                tamano: 'Sin especificar',
                cantidad: 1
            });
        }
        
    } catch (error) {
        console.error('❌ Error en manejarAgregarCarrito:', error);
    }
}

// =============================================
// CONFIRMAR PERSONALIZACIÓN (SEGURO)
// =============================================
function confirmarPersonalizacion() {
    try {
        if (!productoSeleccionado) {
            console.error('❌ No hay producto seleccionado');
            return;
        }

        // Obtener valores del modal de forma segura
        const telaElement = document.getElementById('modalTela');
        const tamañoElement = document.getElementById('modalTamaño');
        const cantidadElement = document.getElementById('modalCantidad');

        const tela = telaElement ? telaElement.value || "Sin especificar" : "Sin especificar";
        const tamaño = tamañoElement ? tamañoElement.value : "";
        const cantidad = cantidadElement ? parseInt(cantidadElement.value) || 1 : 1;

        // Validar tamaño
        if (!tamaño) {
            alert('Por favor selecciona un tamaño para tu vehículo');
            if (tamañoElement) tamañoElement.focus();
            return;
        }

        // Crear producto completo
        const productoCompleto = {
            id: productoSeleccionado.id,
            nombre: productoSeleccionado.nombre,
            precio: productoSeleccionado.precio,
            imagen: productoSeleccionado.imagen,
            tela: tela,
            tamano: tamaño,
            cantidad: cantidad
        };

        // Agregar al carrito
        const exito = agregarProductoAlCarrito(productoCompleto);

        if (exito) {
            // Cerrar modal
            const modalElement = document.getElementById('modalPersonalizar');
            if (modalElement && typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            }

            // Resetear formulario
            resetearFormularioModal();

            // Mostrar confirmación
            mostrarToast('Producto agregado al carrito', 'success');
        }
        
    } catch (error) {
        console.error('❌ Error en confirmarPersonalizacion:', error);
    }
}

// =============================================
// AGREGAR PRODUCTO AL CARRITO (SEGURO)
// =============================================
function agregarProductoAlCarrito(producto) {
    try {
        console.log('🛍️ Agregando producto:', producto);
        
        // Validar producto
        if (!producto || !producto.id) {
            console.error('❌ Producto inválido');
            return false;
        }

        let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

        // Buscar si ya existe el mismo producto con misma configuración
        const indiceExistente = carrito.findIndex(item => 
            parseInt(item.id) === parseInt(producto.id) && 
            item.tela === producto.tela && 
            item.tamano === producto.tamano
        );

        if (indiceExistente !== -1) {
            // Si existe, aumentar cantidad
            carrito[indiceExistente].cantidad += producto.cantidad;
            console.log('➕ Cantidad actualizada');
        } else {
            // Si no existe, agregar nuevo
            carrito.push(producto);
            console.log('🆕 Producto nuevo agregado');
        }

        // Guardar en localStorage
        localStorage.setItem('carrito', JSON.stringify(carrito));
        
        // Actualizar contador
        actualizarContador();
        
        // Sincronizar con servidor (si el usuario está logueado)
        sincronizarCarrito();
        
        return true;
        
    } catch (error) {
        console.error('❌ Error agregando producto al carrito:', error);
        return false;
    }
}

// =============================================
// FUNCIONES DE CARRITO (para carrito.php)
// =============================================
function cargarCarrito() {
    try {
        const contenedor = document.getElementById('carrito-contenido');
        const vacio = document.getElementById('carrito-vacio');
        const totalEl = document.getElementById('carrito-total');
        const btnContinuar = document.getElementById('btn-continuar');
        const resumenDetalle = document.getElementById('resumen-detalle');

        if (!contenedor) {
            console.warn('⚠️ No se encontró el contenedor del carrito');
            return;
        }

        let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
        contenedor.innerHTML = '';

        let total = 0;
        let detalleResumenHTML = '';

        if (carrito.length === 0) {
            if (vacio) vacio.classList.remove('d-none');
            if (btnContinuar) btnContinuar.classList.add('disabled');
            if (totalEl) totalEl.textContent = "$0,00";
            if (resumenDetalle) resumenDetalle.innerHTML = '';
            actualizarContador();
            return;
        }

        if (vacio) vacio.classList.add('d-none');
        if (btnContinuar) btnContinuar.classList.remove('disabled');

        carrito.forEach((prod, index) => {
            const subtotal = (prod.precio || 0) * (prod.cantidad || 0);
            total += subtotal;

            contenedor.innerHTML += `
              <div class="card shadow-sm mb-3">
                <div class="card-body">
                  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                    <div>
                      <h6 class="fw-semibold mb-1">${prod.nombre || 'Producto sin nombre'}</h6>
                      <p class="mb-0 text-muted small">Tela: ${prod.tela || 'Sin especificar'} | Tamaño: ${prod.tamano || 'Sin especificar'}</p>
                      <p class="mb-0 text-muted small">Precio unitario: $${(prod.precio || 0).toLocaleString('es-AR', { minimumFractionDigits: 2 })}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <button class="btn btn-outline-secondary btn-sm" onclick="cambiarCantidad(${index}, -1)">-</button>
                      <input type="number" min="1" id="cantidad-${index}" value="${prod.cantidad || 1}" class="form-control form-control-sm text-center" style="width: 60px;" onchange="cambiarCantidadDirecto(${index}, this.value)">
                      <button class="btn btn-outline-secondary btn-sm" onclick="cambiarCantidad(${index}, 1)">+</button>
                    </div>
                    <div class="text-end">
                      <p class="mb-0 small">Subtotal:</p>
                      <p class="fw-bold mb-0">$${subtotal.toLocaleString('es-AR', { minimumFractionDigits: 2 })}</p>
                    </div>
                    <button class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              </div>`;

            detalleResumenHTML += `<p class="mb-1">${prod.nombre || 'Producto'} (${prod.tela || 'Sin especificar'}, ${prod.tamano || 'Sin especificar'}) x ${prod.cantidad || 1} = $${subtotal.toLocaleString('es-AR', { minimumFractionDigits: 2 })}</p>`;
        });

        if (totalEl) totalEl.textContent = `$${total.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`;
        if (resumenDetalle) resumenDetalle.innerHTML = detalleResumenHTML;
        
        actualizarContador();
        
    } catch (error) {
        console.error('❌ Error cargando carrito:', error);
    }
}

// =============================================
// FUNCIONES DE MANIPULACIÓN DEL CARRITO (SEGURAS)
// =============================================
window.cambiarCantidad = function(index, cambio) {
    try {
        let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
        
        if (carrito[index]) {
            carrito[index].cantidad = (carrito[index].cantidad || 0) + cambio;
            
            if (carrito[index].cantidad <= 0) {
                carrito.splice(index, 1);
            }
            
            localStorage.setItem('carrito', JSON.stringify(carrito));
            cargarCarrito();
            sincronizarCarrito();
        }
    } catch (error) {
        console.error('❌ Error cambiando cantidad:', error);
    }
};

window.cambiarCantidadDirecto = function(index, valor) {
    try {
        let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
        const nuevaCantidad = parseInt(valor, 10);
        
        if (carrito[index]) {
            if (isNaN(nuevaCantidad) || nuevaCantidad <= 0) {
                carrito.splice(index, 1);
            } else {
                carrito[index].cantidad = nuevaCantidad;
            }
            
            localStorage.setItem('carrito', JSON.stringify(carrito));
            cargarCarrito();
            sincronizarCarrito();
        }
    } catch (error) {
        console.error('❌ Error cambiando cantidad directa:', error);
    }
};

window.eliminarProducto = function(index) {
    try {
        let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
        carrito.splice(index, 1);
        localStorage.setItem('carrito', JSON.stringify(carrito));
        cargarCarrito();
        sincronizarCarrito();
    } catch (error) {
        console.error('❌ Error eliminando producto:', error);
    }
};

// =============================================
// FUNCIONES DE UTILIDAD (SEGURAS)
// =============================================
function actualizarContador() {
    try {
        const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
        const total = carrito.reduce((acc, p) => acc + ((p && p.cantidad) ? p.cantidad : 0), 0);
        const badge = document.getElementById('contador-carrito');
        if (badge) {
            badge.textContent = total;
        }
    } catch (error) {
        console.error('❌ Error actualizando contador:', error);
    }
}

// Alias para compatibilidad
window.actualizarBadgeCarrito = actualizarContador;

function sincronizarCarrito() {
    try {
        const bodyElement = document.body;
        const usuarioLogueado = bodyElement && bodyElement.dataset && bodyElement.dataset.usuario === "1";
        if (!usuarioLogueado) return;

        const carrito = JSON.parse(localStorage.getItem('carrito')) || [];
        fetch('sincronizar_carrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(carrito)
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('❌ Error en sincronización:', data.error);
            }
        })
        .catch(error => console.error('❌ Error conectando con servidor:', error));
    } catch (error) {
        console.error('❌ Error en sincronización:', error);
    }
}

function mostrarModalLogin() {
    try {
        const loginModalElement = document.getElementById('loginModal');
        if (loginModalElement && typeof bootstrap !== 'undefined') {
            const loginModal = new bootstrap.Modal(loginModalElement);
            loginModal.show();
        } else {
            alert('Debes iniciar sesión para agregar productos al carrito');
        }
    } catch (error) {
        console.error('❌ Error mostrando modal login:', error);
    }
}

function mostrarToast(mensaje, tipo = 'success') {
    try {
        const toastElement = document.getElementById('toastCarrito');
        if (toastElement) {
            const toastBody = toastElement.querySelector('.toast-body');
            if (toastBody) {
                toastBody.textContent = mensaje;
            }
            
            // Cambiar color según tipo
            toastElement.className = `toast align-items-center text-white border-0`;
            switch(tipo) {
                case 'success':
                    toastElement.classList.add('bg-success');
                    break;
                case 'danger':
                    toastElement.classList.add('bg-danger');
                    break;
                case 'warning':
                    toastElement.classList.add('bg-warning', 'text-dark');
                    break;
                default:
                    toastElement.classList.add('bg-primary');
            }
            
            if (typeof bootstrap !== 'undefined') {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
        } else {
            // Fallback si no hay toast
            alert(mensaje);
        }
    } catch (error) {
        console.error('❌ Error mostrando toast:', error);
        alert(mensaje); // Fallback
    }
}

function resetearFormularioModal() {
    try {
        const cantidadElement = document.getElementById('modalCantidad');
        const tamañoElement = document.getElementById('modalTamaño');
        const telaElement = document.getElementById('modalTela');
        
        if (cantidadElement) cantidadElement.value = 1;
        if (tamañoElement) tamañoElement.value = '';
        if (telaElement) telaElement.selectedIndex = 0;
    } catch (error) {
        console.error('❌ Error reseteando formulario:', error);
    }
}

// =============================================
// FUNCIÓN PARA AGREGAR DESDE PRODUCTOS.PHP (SEGURA)
// =============================================
window.agregarAlCarrito = function(producto) {
    try {
        const bodyElement = document.body;
        const usuarioLogueado = bodyElement && bodyElement.dataset && bodyElement.dataset.usuario === "1";
        
        if (!usuarioLogueado) {
            mostrarModalLogin();
            return;
        }

        const productoCarrito = {
            id: parseInt(producto.id) || 0,
            nombre: producto.nombre || 'Producto sin nombre',
            precio: parseFloat(producto.precio) || 0,
            imagen: producto.imagen || 'default.jpg',
            tela: 'Sin especificar',
            tamano: 'Sin especificar',
            cantidad: 1
        };

        const exito = agregarProductoAlCarrito(productoCarrito);
        if (exito) {
            mostrarToast(`"${producto.nombre}" agregado al carrito`);
        }
    } catch (error) {
        console.error('❌ Error en agregarAlCarrito:', error);
    }
};

console.log('✅ Carrito.js cargado completamente');