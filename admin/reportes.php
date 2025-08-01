<?php include "layout.php"; ?>
<?php require_once "../conexion.php";
$labels=[]; $data=[];
$res = $conn->query("
  SELECT DATE_FORMAT(p.fecha,'%Y-%m') AS mes, SUM(dp.precio_unitario*dp.cantidad) AS total
  FROM pedidos p
  JOIN detalle_pedido dp ON p.id=dp.pedido_id
  WHERE p.status='approved'
  GROUP BY mes ORDER BY mes
");
while($r=$res->fetch_assoc()){$labels[]=$r['mes']; $data[]=$r['total'];}
?>
<div class="content-header mb-3">
  <h1 class="m-0">Reportes</h1>
</div>
<div class="card">
  <div class="card-body">
    <canvas id="ventasChart" style="width:100%;max-width:800px;"></canvas>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('ventasChart').getContext('2d');
new Chart(ctx,{
  type:'bar',
  data:{labels:<?=json_encode($labels)?>,datasets:[{label:'Ventas Mensuales',data:<?=json_encode($data)?>,backgroundColor:'rgba(40,167,69,0.7)'}]},
  options:{responsive:true,scales:{y:{beginAtZero:true}}}
});
</script>
<?php include "footer_layout.php"; ?>