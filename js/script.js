document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);

  // Mostrar el modal de login si hubo un error
  if (params.has("error")) {
    const loginModalElement = document.getElementById("loginModal");
    if (loginModalElement) {
      const loginModal = new bootstrap.Modal(loginModalElement);
      loginModal.show();
    }
  }

  // Toast dinámico
  const toastBox = document.getElementById("toastBox");
  const texto = document.getElementById("toastText");

  if (toastBox && texto) {
    const toast = new bootstrap.Toast(toastBox);

    if (params.has("registro") && params.get("registro") === "exito") {
      texto.textContent = "✅ ¡Registro exitoso! Ya podés comprar.";
      toastBox.classList.remove("bg-primary", "bg-danger", "bg-info");
      toastBox.classList.add("bg-success");
      toast.show();
    }

    if (params.has("error")) {
      texto.textContent = "⚠️ Hubo un error al procesar tu solicitud.";
      toastBox.classList.remove("bg-primary", "bg-success", "bg-info");
      toastBox.classList.add("bg-danger");
      toast.show();
    }

    if (params.has("recuperar") && params.get("recuperar") === "enviado") {
      texto.textContent = "📧 Enviamos un enlace a tu correo.";
      toastBox.classList.remove("bg-primary", "bg-danger", "bg-success");
      toastBox.classList.add("bg-info");
      toast.show();
    }
  }
});


// Cargar carrito desde BD tras login exitoso
const params = new URLSearchParams(window.location.search);
if (params.get('login') === 'exito') {
  fetch('obtener_carrito.php')
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        localStorage.setItem('carrito', JSON.stringify(data.carrito));
        actualizarContador();
        location.reload();
      } else {
        console.error('Error al cargar carrito:', data.error);
      }
    })
    .catch(error => {
      console.error('Error en la petición:', error);
    });
}
