<?php
session_start();
header('Content-Type: application/json');

// Nur eingeloggte Kunden dürfen Daten ändern
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Nicht erlaubt.']);
    exit;
}

require_once '../../config/dbaccess.php';
require_once '../../models/User.class.php';

$db = new DBAccess();
$pdo = $db->pdo;
$user = new User($pdo);

$userId = $_SESSION['user']['id'];

// Nur diese Felder dürfen geändert werden
$allowedFields = ['address', 'postalcode', 'city', 'payment_info', 'username'];
$dataToUpdate = [];

foreach ($allowedFields as $field) {
    if (isset($_POST[$field]) && $_POST[$field] !== '') {
        $dataToUpdate[$field] = $_POST[$field];
    }
}

if (empty($dataToUpdate)) {
    echo json_encode(['success' => false, 'message' => 'Keine gültigen Felder übermittelt.']);
    exit;
}

// Passwortprüfung ist Pflicht
if (!isset($_POST['current_password']) || trim($_POST['current_password']) === '') {
    echo json_encode(['success' => false, 'message' => 'Passwort ist erforderlich.']);
    exit;
}

// Passwort aus DB laden
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$userId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user->checkPassword($userId, $_POST['current_password'])) {
    echo json_encode(['success' => false, 'message' => 'Falsches Passwort.']);
    exit;
}

// Änderungen speichern
$success = $user->updatePartial($userId, $dataToUpdate);

if ($success) {
    $_SESSION['user'] = array_merge($_SESSION['user'], $dataToUpdate);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren.']);
}
