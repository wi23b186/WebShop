<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Nicht erlaubt.']);
    exit;
}

require_once '../config/dbaccess.php';
$db = new DBAccess();
$pdo = $db->pdo;

$userId = $_SESSION['user']['id'];
$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';

if (strlen($new) < 6) {
    echo json_encode(['success' => false, 'message' => 'Neues Passwort ist zu kurz (min. 6 Zeichen).']);
    exit;
}

// Hole aktuelles Passwort aus DB
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || !password_verify($current, $row['password'])) {
    echo json_encode(['success' => false, 'message' => 'Aktuelles Passwort ist falsch.']);
    exit;
}

// Neues Passwort speichern
$newHash = password_hash($new, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$newHash, $userId]);

echo json_encode(['success' => true]);
