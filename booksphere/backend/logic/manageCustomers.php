<?php
session_start();
require_once '../config/dbaccess.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Zugriff verweigert.']);
    exit;
}

$db = new DBAccess();
$pdo = $db->pdo;

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

if ($action === 'getCustomers') {
    $stmt = $pdo->query("SELECT id, username, email, active FROM users WHERE role = 'customer'");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'toggleActive') {
    $userId = $_POST['user_id'] ?? 0;
    $active = $_POST['active'] ?? 0;

    $stmt = $pdo->prepare("UPDATE users SET active = ? WHERE id = ?");
    $success = $stmt->execute([$active, $userId]);

    echo json_encode(['success' => $success]);
    exit;
}

if ($action === 'getOrders') {
    $userId = $_GET['user_id'] ?? 0;

    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as &$order) {
        $stmtItems = $pdo->prepare("
            SELECT oi.id, oi.product_id, oi.quantity, oi.price, p.name 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmtItems->execute([$order['id']]);
        $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($orders);
    exit;
}

if ($action === 'removeOrderItem') {
    $itemId = $_POST['item_id'] ?? 0;

    // Schritt 1: Hole order_id vor dem Löschen
    $stmt = $pdo->prepare("SELECT order_id FROM order_items WHERE id = ?");
    $stmt->execute([$itemId]);
    $orderId = $stmt->fetchColumn();

    // Schritt 2: Lösche das Produkt aus order_items
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE id = ?");
    $success = $stmt->execute([$itemId]);

    if ($success && $orderId) {
        // Schritt 3: Neue Gesamtsumme berechnen
        $stmt = $pdo->prepare("SELECT SUM(price * quantity) FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $newTotal = $stmt->fetchColumn() ?? 0;

        // Schritt 4: Update in orders
        $stmt = $pdo->prepare("UPDATE orders SET total = ? WHERE id = ?");
        $stmt->execute([$newTotal, $orderId]);
    }

    echo json_encode(['success' => $success]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ungültige Aktion']);
