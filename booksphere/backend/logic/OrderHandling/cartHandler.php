<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config/dbaccess.php';
require_once '../../models/Product.class.php';
require_once '../../models/Cart.class.php';

$db = new DBAccess();
$pdo = $db->pdo;
$cart = new Cart($pdo);

// POST-Anfragen: Produkt hinzufügen oder Menge ändern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = $_POST['product_id'] ?? null;
    $change = $_POST['change'] ?? null;

    if ($action === 'add' && $productId) {
        $cart->add($productId);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'updateQuantity' && $productId && $change) {
        $cart->updateQuantity($productId, $change);
        echo json_encode(['success' => true]);
        exit;
    }
}
// GET-Anfragen: Warenkorbinfos abrufen
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'getCount') {
        echo json_encode(['count' => $cart->getCount()]);
        exit;
    }

    if ($action === 'getItems') {
        echo json_encode($cart->getItems());
        exit;
    }
}

// Ungültige Anfrage
echo json_encode(['error' => 'Invalid request']);
?>