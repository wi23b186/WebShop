<?php
// init.php: muss zu Beginn jeder Seite eingebunden werden, bevor irgendeine Ausgabe erfolgt!
session_start();
require_once __DIR__ . '/dataHandler.php'; // enthält die PDO-Verbindung

// Falls der User nicht per Session eingeloggt ist, aber ein gültiges Cookie "remember_me" existiert:
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $userId = $_COOKIE['remember_me'];
    
    // User anhand der customer_id laden
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Session wiederherstellen
        $_SESSION['user_id'] = $user['customer_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = (strtolower($user['username']) === 'admin') ? 'admin' : 'user';
    } else {
        // Wenn kein User gefunden wird, sollte das Cookie gelöscht werden
        setcookie("remember_me", "", time() - 3600, "/");
    }
}
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'guest';
}
?>
