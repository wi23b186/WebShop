<?php
require_once '../../config/dbaccess.php';
require_once '../../models/Voucher.class.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Zugriff verweigert']);
    exit;
}

$db = new DBAccess();
$voucher = new Voucher($db->pdo);

header('Content-Type: application/json');
echo json_encode($voucher->getAll());
?>