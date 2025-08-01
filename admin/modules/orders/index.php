<?php include "layout.php"; ?>
<?php require_once "../conexion.php"; ?>

<div class="content-header mb-3">
  <h1 class="m-0">Pedidos</h1>
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Listado de Pedidos</h3>
  </div>
  <div class="card-body">
    <table id="ordersTable" class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuario</th>
          <th>Fecha</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $res = $conn->query("
          SELECT p.id, u.nombre AS usuario, p.fecha, 
                 IFNULL(SUM(dp.precio_unitario*dp.cantidad),0) AS total, 
                 p.status
          FROM pedidos p
          JOIN usuarios u ON p.usuario_id=u.id
          LEFT JOIN detalle_pedido dp ON p.id=dp.pedido_id
          GROUP BY p.id
          ORDER BY p.fecha DESC
        ");
        while($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['usuario']) ?></td>
          <td><?= date('d/m/Y H:i', strtotime($row['fecha'])) ?></td>
          <td>$<?= number_format($row['total'],2,',','.') ?></td>
          <td>
            <select class="form-control form-control-sm status-change" data-id="<?= $row['id'] ?>" style="width: 120px;">
              <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pendiente</option>
              <option value="approved" <?= $row['status']=='approved'?'selected':'' ?>>Aprobado</option>
              <option value="failure" <?= $row['status']=='failure'?'selected':'' ?>>Rechazado</option>
            </select>
          </td>
          <td>
            <a href="order_detail.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">
              <i class="fas fa-eye"></i> Ver
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
$(function () {
  // Inicializar DataTable
  $('#ordersTable').DataTable({
    responsive: true,
    autoWidth: false,
    language: {
      "sProcessing":     "Procesando...",
      "sLengthMenu":     "Mostrar _MENU_ registros",
      "sZeroRecords":    "No se encontraron resultados",
      "sEmptyTable":     "Ningún dato disponible en esta tabla",
      "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
      "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
      "sSearch":         "Buscar:",
      "oPaginate": {
        "sFirst":    "Primero",
        "sLast":     "Último",
        "sNext":     "Siguiente",
        "sPrevious": "Anterior"
      }
    }
  });
  
  // Manejo de cambio de estado
  $('.status-change').on('change', function() {
    const pedidoId = $(this).data('id');
    const status = $(this).val();
    const $select = $(this);
    
    // Cambiar color según estado
    $select.removeClass('bg-warning bg-success bg-danger');
    if (status === 'pending') $select.addClass('bg-warning');
    else if (status === 'approved') $select.addClass('bg-success');
    else if (status === 'failure') $select.addClass('bg-danger');
    
    // Enviar actualización al servidor
    $.ajax({
      url: 'update_status.php',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        pedido_id: pedidoId, 
        status: status
      }),
      success: function(resp) {
        // Mostrar notificación de éxito
        if (typeof toastr !== 'undefined') {
          toastr.success('Estado actualizado correctamente');
        }
      },
      error: function() {
        alert('Error al actualizar el estado');
        location.reload(); // Recargar para restaurar el estado anterior
      }
    });
  });
  
  // Aplicar colores iniciales a los selects
  $('.status-change').each(function() {
    const status = $(this).val();
    if (status === 'pending') $(this).addClass('bg-warning');
    else if (status === 'approved') $(this).addClass('bg-success');
    else if (status === 'failure') $(this).addClass('bg-danger');
  });
});
</script>

<style>
.status-change {
  color: white;
  border: none;
}
.status-change.bg-warning {
  background-color: #ffc107 !important;
}
.status-change.bg-success {
  background-color: #28a745 !important;
}
.status-change.bg-danger {
  background-color: #dc3545 !important;
}
.status-change option {
  color: black;
  background-color: white;
}
</style>

<?php include "footer_layout.php"; ?>