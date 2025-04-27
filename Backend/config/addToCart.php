<?php
// CORS und Session
header("Access-Control-Allow-Origin: http://localhost/ProjektWebshop/WebShop");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

session_start();
header("Content-Type: application/json; charset=utf-8");

// JSON einlesen
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['id'] ?? null;
if (!$productId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Keine Produkt-ID erhalten.']);
    exit;
}

// Warenkorb initialisieren
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Menge erhöhen oder neu anlegen
if (isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId]++;
} else {
    $_SESSION['cart'][$productId] = 1;
}

// Gesamtanzahl
$cartItemCount = array_sum($_SESSION['cart']);

// Ergebnis
echo json_encode([
    'success'       => true,
    'message'       => 'Produkt wurde in den Warenkorb gelegt.',
    'cartItemCount' => $cartItemCount
]);
