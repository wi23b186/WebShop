<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

// JSON aus Request
$body = json_decode(file_get_contents('php://input'), true);
$id   = $body['id'] ?? null;

// Entfernen, wenn vorhanden
if ($id !== null && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

// Neue Anzahl
$totalCount = array_sum($_SESSION['cart'] ?? []);

echo json_encode([
    'success'     => true,
    'totalCount'  => $totalCount
]);
?>