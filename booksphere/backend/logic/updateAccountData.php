<?php
session_start();
header('Content-Type: application/json');

// Prüfen, ob ein eingeloggter Benutzer mit Rolle 'customer' existiert
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Nicht erlaubt.']);
    exit;
}

// Datenbank- und User-Klasse laden
require_once '../config/dbaccess.php';
require_once '../models/User.class.php';

$db = new DBAccess();
$pdo = $db->pdo;
$user = new User($pdo);

$userId = $_SESSION['user']['id'];

// Felder, die aktualisiert werden dürfen
$allowedFields = ['address', 'postalcode', 'city', 'payment_info', 'username'];

// Daten aus POST extrahieren
$dataToUpdate = [];
foreach ($allowedFields as $field) {
    if (isset($_POST[$field]) && $_POST[$field] !== '') {
        $dataToUpdate[$field] = $_POST[$field];
    }
}

// Wenn keine gültigen Felder vorhanden sind
if (empty($dataToUpdate)) {
    echo json_encode([
        'success' => false,
        'message' => 'Keine gültigen Felder übermittelt.'
    ]);
    exit;
}

// Update durchführen
$success = $user->updatePartial($userId, $dataToUpdate);

if ($success) {
    // Session aktualisieren mit den neuen Werten
    $_SESSION['user'] = array_merge($_SESSION['user'], $dataToUpdate);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren.']);
}
?>
