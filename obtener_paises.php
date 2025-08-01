<?php
$conexion = new mysqli("localhost", "root", "", "covercars");
$paises = $conexion->query("SELECT id, nombre FROM paises ORDER BY nombre");
echo json_encode($paises->fetch_all(MYSQLI_ASSOC));
