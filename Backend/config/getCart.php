<?php
// CORS und Session
header("Access-Control-Allow-Origin: http://localhost/ProjektWebshop/WebShop");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

session_start();
header("Content-Type: application/json; charset=utf-8");

// DB-Verbindung
require_once 'dataHandler.php';

// Session-Warenkorb
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo json_encode([
        'items'      => [],
        'totalPrice' => 0,
        'totalCount' => 0
    ]);
    exit;
}

// Produkte aus DB laden
$productIds  = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$query        = "SELECT * FROM books WHERE id IN ($placeholders)";
$stmt         = $pdo->prepare($query);
$stmt->execute($productIds);
$products     = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ergebnis aufbauen
$cartItems  = [];
$totalPrice = 0;
foreach ($products as $product) {
    $id        = $product['id'];
    $quantity  = $cart[$id];
    $subtotal  = $quantity * $product['price'];
    $product['quantity'] = $quantity;
    $product['subtotal'] = $subtotal;
    $totalPrice += $subtotal;
    $cartItems[] = $product;
}
$totalCount = array_sum($cart);

echo json_encode([
    'items'      => $cartItems,
    'totalPrice' => $totalPrice,
    'totalCount' => $totalCount
]);
?>