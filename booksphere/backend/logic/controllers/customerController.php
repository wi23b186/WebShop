<?php
//alter weiß ich nicht mehr :)
session_start();
require_once '../../config/dbaccess.php';
require_once '../../models/User.class.php';
require_once '../../models/Order.class.php';

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

    echo json_encode($order->removeOrderItemAndUpdateTotal($itemId));
    exit;
}

if ($_POST['action'] === 'deleteOrder' && isset($_POST['order_id'])) {
    $orderId = (int) $_POST['order_id'];
    
    // Sicherstellen, dass die Bestellung zum User gehört
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);

    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);

    echo json_encode(['success' => true]);
    exit;
}



echo json_encode(['success' => false, 'message' => 'Ungültige Aktion']);
