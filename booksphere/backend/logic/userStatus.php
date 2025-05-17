<?php
session_start();
require_once '../config/dbaccess.php';

$db = new DBAccess();

if (!isset($_SESSION['user']) && isset($_COOKIE['remember_user'])) {
    $stmt = $db->pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_COOKIE['remember_user']]);
    $rememberedUser = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rememberedUser) {
        $_SESSION['user'] = $rememberedUser;
    }
}

if (isset($_SESSION['user'])) {
    echo json_encode([
        'loggedIn' => true,
        'username' => $_SESSION['user']['username'],
        'role' => $_SESSION['user']['role']
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
