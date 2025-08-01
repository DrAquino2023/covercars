<?php
/**
 * generar_pago.php
 * — crea el pedido en la BD
 * — arma la preferencia de Mercado Pago (Checkout Pro)
 * — redirige al init_point
 */

require_once __DIR__ . '/vendor/autoload.php';  // SDK Mercado Pago
$cfg = require __DIR__ . '/inc/config.php';     // ['mp_access_token'=>…]
require_once __DIR__ . '/inc/conexion.php';     // abre $conn (mysqli)

session_start();

/* ──────────────────────────────
 * 1│ RECUPERAR CARRITO Y USUARIO
 * ────────────────────────────── */
$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) {
    die('No hay ítems en el carrito');
}

$usuarioId = $_SESSION['user']['id']   ?? null;
$nombre    = $_SESSION['user']['name'] ?? '';
$email     = $_SESSION['user']['mail'] ?? '';
$direccion = $_SESSION['user']['addr'] ?? '';

/* ──────────────────────────────
 * 2│ INSERTAR PEDIDO (PENDING)
 * ────────────────────────────── */
$total = array_reduce($carrito, fn($s, $p) => $s + ($p['precio'] * $p['cantidad']), 0);

$stmt = $conn->prepare(
    "INSERT INTO pedidos (usuario_id, fecha, total, nombre, email, direccion, status)
     VALUES (?,        NOW(), ?,    ?,      ?,    ?,         'pending')"
);
$stmt->bind_param('idsss', $usuarioId, $total, $nombre, $email, $direccion);
$stmt->execute();
$pedidoId = $conn->insert_id;                       // 🔑 clave primaria recién creada

/* ──────────────────────────────
 * 3│ CONFIGURAR SDK    TOKEN PROD
 * ────────────────────────────── */
MercadoPago\SDK::setAccessToken($cfg['mp_access_token']);

/* ──────────────────────────────
 * 4│ ARMAR PREFERENCIA
 * ────────────────────────────── */
$preference = new MercadoPago\Preference();

/* — Ítems — */
$preference->items = array_map(function ($prod) {
    $item = new MercadoPago\Item();
    $item->id          = $prod['id'];
    $item->title       = $prod['nombre'];
    $item->unit_price  = (float) $prod['precio'];
    $item->quantity    = (int)   $prod['cantidad'];
    return $item;
}, $carrito);

/* — Callbacks & Webhook — */
$baseUrl = 'https://' . $_SERVER['HTTP_HOST'];  // funciona local si usás ngrok
$preference->back_urls = [
    'success'  => "$baseUrl/pago_exitoso.php",
    'failure'  => "$baseUrl/pago_error.php",
    'pending'  => "$baseUrl/pago_pendiente.php"
];
$preference->auto_return      = 'approved';
$preference->notification_url = "$baseUrl/ipn_mercadopago.php";

/* — VÍNCULO PEDIDO ↔️ PAGO — */
$preference->external_reference = $pedidoId;   // ⭐¡IMPORTANTE!

$preference->save();

/* ──────────────────────────────
 * 5│ REDIRIGIR AL CHECKOUT
 * ────────────────────────────── */
header('Location: ' . $preference->init_point); // producción
exit;
