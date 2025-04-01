<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
header('Content-Type: application/json');

require_once __DIR__ . '/dataHandler.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $title = trim($data['title'] ?? '');
    $firstName = trim($data['firstName'] ?? '');
    $lastName = trim($data['lastName'] ?? '');
    $address = trim($data['address'] ?? '');
    $zip = trim($data['zip'] ?? '');
    $city = trim($data['city'] ?? '');
    $email = trim($data['email'] ?? '');
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirmPassword'] ?? '';
    $paymentInfo = trim($data['paymentInfo'] ?? '');

    // Serverseitige Validierung: Alle Felder müssen vorhanden sein
    if (
        empty($title) || empty($firstName) || empty($lastName) ||
        empty($address) || empty($zip) || empty($city) || empty($email) ||
        empty($username) || empty($password) || empty($confirmPassword) ||
        empty($paymentInfo)
    ) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Alle Felder sind erforderlich."]);
        exit;
    }

    if ($password !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Die Passwörter stimmen nicht überein."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Ungültige Emailadresse."]);
        exit;
    }

    if (!preg_match('/^\d{4,}$/', $zip)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Ungültige PLZ."]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO customers (title, first_name, last_name, address, zip_code, city, email, username, password, payment_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $firstName, $lastName, $address, $zip, $city, $email, $username, $hashedPassword, $paymentInfo]);

    echo json_encode(["success" => true, "message" => "Benutzer erfolgreich registriert!"]);
    exit;
}
?>
