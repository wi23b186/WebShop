<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Nicht erlaubt.']);
    exit;
}
require_once '../../config/dbaccess.php';
require_once '../../models/User.class.php';

$db = new DBAccess();
$userModel = new User($db->pdo);

$userId = $_SESSION['user']['id'];
$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';

$result = $userModel->changePassword($userId, $current, $new);
echo json_encode($result);
?>