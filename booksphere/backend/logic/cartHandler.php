<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once '../config/dbaccess.php';

$db = new DBAccess();
$pdo = $db->pdo;

// Initialisiere Warenkorb
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add') {
        $id = $_POST['product_id'];
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
        echo json_encode(['success' => true]);
        exit;
    }

    if ($_POST['action'] === 'updateQuantity') {
        $id = $_POST['product_id'];
        $change = $_POST['change'];

        if ($change === 'remove') {
            unset($_SESSION['cart'][$id]);
        } elseif (isset($_SESSION['cart'][$id])) {
            if ($change === 'increase') {
                $_SESSION['cart'][$id]++;
            } elseif ($change === 'decrease') {
                $_SESSION['cart'][$id]--;
                if ($_SESSION['cart'][$id] <= 0) {
                    unset($_SESSION['cart'][$id]);
                }
            }
        }

        echo json_encode(['success' => true]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['action'] === 'getCount') {
        $count = array_sum($_SESSION['cart']);
        echo json_encode(['count' => $count]);
        exit;
    }

    if ($_GET['action'] === 'getItems') {
        $items = [];
        foreach ($_SESSION['cart'] as $id => $qty) {
            $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $product['quantity'] = $qty;
                $items[] = $product;
            }
        }
        echo json_encode($items);
        exit;
    }
}

echo json_encode(['error' => 'Invalid request']);
?>
