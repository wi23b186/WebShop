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

$action = $_REQUEST['action'] ?? '';

if ($action === 'getAll') {
    $stmt = $pdo->query("SELECT * FROM products");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'create') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $rating = $_POST['rating'] ?? 0;

    if (empty($name) || empty($description) || empty($category) || empty($_FILES['image'])) {
        echo json_encode(['success' => false, 'message' => 'Alle Felder inkl. Bild sind Pflicht.']);
        exit;
    }

    // Bild hochladen
    $targetDir = '../productpictures/';
    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
    $targetFile = $targetDir . $filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Hochladen des Bildes.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, rating, image) VALUES (?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([$name, $description, $price, $category, $rating, $filename]);

    echo json_encode(['success' => $success]);
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $success = $stmt->execute([$id]);
    echo json_encode(['success' => $success]);
    exit;
}

if ($action === 'update') {
    $id = $_POST['id'] ?? 0;
    $field = $_POST['field'] ?? '';
    $value = $_POST['value'] ?? '';

    file_put_contents('log.txt', "UPDATE: id=$id, field=$field, value=$value\n", FILE_APPEND);

    if (!in_array($field, ['name', 'description', 'price', 'category', 'rating'])) {
        echo json_encode(['success' => false, 'message' => 'Ungültiges Feld.']);
        exit;
    }

    $sql = "UPDATE products SET `$field` = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$value, $id]);

    if (!$success) {
        $error = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => $error[2]]);
        exit;
    }

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ungültige Aktion.']);
