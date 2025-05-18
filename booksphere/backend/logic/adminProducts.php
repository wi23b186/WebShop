<?php
session_start();
require_once '../config/dbaccess.php';
require_once '../models/Product.class.php';

header('Content-Type: application/json');

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
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category = $_POST['category'] ?? '';
        $rating = $_POST['rating'] ?? 0;

        if (empty($name) || empty($description) || empty($category) || empty($_FILES['image'])) {
            echo json_encode(['success' => false, 'message' => 'Alle Felder inkl. Bild sind Pflicht.']);
            break;
        }

        $targetDir = '../productpictures/';
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Hochladen des Bildes.']);
            break;
        }

        $success = $product->create($name, $description, $price, $category, $rating, $filename);
        echo json_encode(['success' => $success]);
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

        if (!$success) {
            echo json_encode(['success' => false, 'message' => $message]);
        } else {
            echo json_encode(['success' => true]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Ungültige Aktion.']);
}
