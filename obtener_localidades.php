<?php
$conexion = new mysqli("localhost", "root", "", "covercars");
$id = intval($_GET['provincia_id']);
$localidades = $conexion->query("SELECT id, nombre FROM localidades WHERE provincia_id = $id ORDER BY nombre");
echo json_encode($localidades->fetch_all(MYSQLI_ASSOC));
