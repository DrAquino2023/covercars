document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".status-select").forEach(select => {
    select.addEventListener("change", () => {
      const pedidoId = select.getAttribute("data-id");
      const status = select.value;
      fetch("update_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ pedido_id: pedidoId, status: status })
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          alert("Error al actualizar estado: " + (data.error||""));
        }
      })
      .catch(err => {
        console.error("Error AJAX:", err);
        alert("Error de conexión");
      });
    });
  });
});