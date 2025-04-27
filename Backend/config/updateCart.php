<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$qty = isset($data['quantity']) ? (int)$data['quantity'] : null;

// Validierung
if (!$id || $qty < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige Parameter.']);
    exit;
}

if (!isset($_SESSION['cart'][$id])) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Produkt nicht im Warenkorb gefunden.']);
    exit;
}

// Setze neue Menge
$_SESSION['cart'][$id] = $qty;

$cartItemCount = array_sum($_SESSION['cart']);

// Rückgabe
echo json_encode([
    'success'       => true,
    'message'       => 'Warenkorb aktualisiert.',
    'cartItemCount' => $cartItemCount
]);
?>
