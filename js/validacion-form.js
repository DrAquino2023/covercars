document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("form-contacto");
    if (!form) return;
  
    form.addEventListener("submit", function (event) {
      const nombre = document.getElementById("nombre");
      const email = document.getElementById("email");
      const mensaje = document.getElementById("mensaje");
  
      if (nombre.value.trim() === "" || email.value.trim() === "" || mensaje.value.trim() === "") {
        alert("Por favor, completá todos los campos.");
        event.preventDefault();
        return;
      }
  
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email.value)) {
        alert("Ingresá un correo electrónico válido.");
        event.preventDefault();
        return;
      }
  
      if (mensaje.value.trim().length < 10) {
        alert("El mensaje debe tener al menos 10 caracteres.");
        event.preventDefault();
      }
    });
  
    const alerta = document.getElementById("alerta-contacto");
    if (alerta) {
      setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
        bsAlert.close();
      }, 5000);
    }
  });
  