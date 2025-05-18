<?php
session_start();
require_once '../../config/dbaccess.php';
require_once '../../models/Voucher.class.php';

if (!isset($_GET['code'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Kein Code übergeben']);
    exit;
}

$db = new DBAccess();
$pdo = $db->pdo;
$voucherModel = new Voucher($pdo);

$code = $_GET['code'];
$voucher = $voucherModel->validate($code);

if (!$voucher) {
    http_response_code(404);
    echo json_encode(['error' => 'Ungültiger oder abgelaufener Code']);
    exit;
}

$available = $voucher['value'] - $voucher['used_value'];

if ($available <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Gutschein wurde bereits vollständig eingelöst']);
    exit;
}

echo json_encode([
    'valid' => true,
    'available' => $available
]);
?>