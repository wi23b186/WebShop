<?php
session_start();
require_once '../../config/dbaccess.php';
require_once '../../models/Order.class.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Nicht eingeloggt']);
    exit;
}

$userId = $_SESSION['user']['id'];
$pdo = (new DBAccess())->pdo;

$orderModel = new Order($pdo);
$orders = $orderModel->getOrdersByUser($userId);

header('Content-Type: application/json');
echo json_encode($orders);
?>
