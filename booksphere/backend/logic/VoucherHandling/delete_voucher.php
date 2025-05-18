<?php
require_once '../../config/dbaccess.php';
require_once '../../models/Voucher.class.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false]);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false]);
    exit;
}

$db = new DBAccess();
$voucher = new Voucher($db->pdo);

echo json_encode(['success' => $voucher->delete($id)]);
?>