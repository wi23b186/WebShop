<?php
session_start();
require_once '../config/dbaccess.php';
require_once '../models/User.class.php';
require_once '../models/Order.class.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Zugriff verweigert.']);
    exit;
}

$db = new DBAccess();
$pdo = $db->pdo;
$user = new User($pdo);
$order = new Order($pdo);

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

if ($action === 'getCustomers') {
    echo json_encode($user->getAllCustomers());
    exit;
}

if ($action === 'toggleActive') {
    $userId = $_POST['user_id'] ?? 0;
    $active = $_POST['active'] ?? 0;
    $success = $user->setActive($userId, $active);
    echo json_encode(['success' => $success]);
    exit;
}

if ($action === 'getOrders') {
    $userId = $_GET['user_id'] ?? 0;
    $orders = $order->getOrdersByUser($userId);
    echo json_encode($orders);
    exit;
}

if ($action === 'removeOrderItem') {
    $itemId = $_POST['item_id'] ?? 0;

    if (!$itemId) {
        echo json_encode(['success' => false, 'message' => 'Keine item_id übergeben']);
        exit;
    }

    // Hole order_id
    $stmt = $pdo->prepare("SELECT order_id FROM order_items WHERE id = ?");
    $stmt->execute([$itemId]);
    $orderId = $stmt->fetchColumn();

    if (!$orderId) {
        echo json_encode(['success' => false, 'message' => 'Order-ID nicht gefunden']);
        exit;
    }

    // Lösche das Produkt aus order_items
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE id = ?");
    $success = $stmt->execute([$itemId]);

    if ($success) {
        // Neue Gesamtsumme berechnen
        $stmt = $pdo->prepare("SELECT SUM(price * quantity) FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $newTotal = $stmt->fetchColumn();
        if ($newTotal === null) $newTotal = 0;

        // Update orders.total
        $stmt = $pdo->prepare("UPDATE orders SET total = ? WHERE id = ?");
        $stmt->execute([$newTotal, $orderId]);

        echo json_encode(['success' => true, 'newTotal' => $newTotal]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Löschen fehlgeschlagen']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ungültige Aktion']);
