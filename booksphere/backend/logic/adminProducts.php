<?php
session_start();
header('Content-Type: application/json');
require_once '../config/dbaccess.php';
require_once '../models/Product.class.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Zugriff verweigert.']);
    exit;
}

$db = new DBAccess();
$product = new Product($db->pdo);
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'getAll':
        echo json_encode($product->getAll());
        break;

    case 'create':
        list($success, $message) = $product->create($_POST, $_FILES['image'] ?? []);
        echo json_encode(['success' => $success, 'message' => $message]);
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;
        $success = $product->delete($id);
        echo json_encode(['success' => $success]);
        break;

    case 'update':
        $id = $_POST['id'] ?? 0;
        $field = $_POST['field'] ?? '';
        $value = $_POST['value'] ?? '';
        list($success, $message) = $product->updateField($id, $field, $value);
        echo json_encode(['success' => $success, 'message' => $message]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Ungültige Aktion.']);
}
?>