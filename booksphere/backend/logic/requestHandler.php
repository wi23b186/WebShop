<?php
session_start();
require_once '../config/dbaccess.php';
require_once '../models/User.class.php';
require_once '../models/Product.class.php';

$db = new DBAccess();
$user = new User($db->pdo);
$product = new Product($db->pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'register') {
        $result = $user->register($_POST);
        if ($result['success']) {
            header('Location: ../../frontend/login.html');
            exit();
        } else {
            echo "Registration failed! <a href='../../frontend/register.html'>Try again</a>";
        }
    } elseif ($_POST['action'] === 'login') {
        $remember = isset($_POST['remember']);
        $success = $user->login($_POST['identifier'], $_POST['password'], $remember);
        if ($success) {
            header('Location: ../../frontend/index.html');
            exit();
        } else {
            echo "Login failed! <a href='../../frontend/login.html'>Try again</a>";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['action'] === 'getProducts') {
        echo json_encode($product->getAllProducts());
    } elseif ($_GET['action'] === 'searchProducts') {
        $query = $_GET['query'] ?? '';
        $category = $_GET['category'] ?? '';

        $sql = "SELECT id, name, description, price, image FROM products WHERE 1=1";
        $params = [];

        if (!empty($query)) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $params[] = "%$query%";
            $params[] = "%$query%";
        }

        if (!empty($category)) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        $stmt = $db->pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } 
    // 🆕 NEU: Einzelnes Produkt per ID holen
    elseif ($_GET['action'] === 'getProductById' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $stmt = $db->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($productData);
    }
}
?>
