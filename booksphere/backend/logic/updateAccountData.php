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
$address = $_POST['address'] ?? '';
$postalcode = $_POST['postalcode'] ?? '';
$city = $_POST['city'] ?? '';
$payment = $_POST['payment_info'] ?? '';
$username = $_POST['username'] ?? '';
$sql = "UPDATE users SET address = ?, postalcode = ?, city = ?, payment_info = ?, username = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$address, $postalcode, $city, $payment, $username, $userId]);


if (!$address || !$postalcode || !$city || !$payment || !$username) {
    echo json_encode(['success' => false, 'message' => 'Alle Felder bis auf Passwort sind Pflicht.']);
    exit;
}



if ($success) {
    $_SESSION['user']['address'] = $address;
    $_SESSION['user']['postalcode'] = $postalcode;
    $_SESSION['user']['city'] = $city;
    $_SESSION['user']['payment_info'] = $payment;
    $_SESSION['user']['username'] = $username;

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler.']);
}
