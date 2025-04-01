<?php
header("Access-Control-Allow-Origin: *"); // Genaue Origin!
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true"); // Wichtig für Cookies/Session
session_start();
require_once __DIR__ . '/dataHandler.php';



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $loginInput = $data['loginInput'] ?? '';
    $password   = $data['password']   ?? '';
    $remember   = isset($data['remember']) && $data['remember'];
    

    // Suche ausschließlich nach der Email
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ? OR username = ? LIMIT 1");
    $stmt->execute([$loginInput, $loginInput]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['customer_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        // Beispiel: "admin" gilt als Administrator
        $_SESSION['role'] = (strtolower($user['username']) === 'admin') ? 'admin' : 'user';

        if ($remember) {
            // Cookie "remember_me" wird für 30 Tage gesetzt
            setcookie("remember_me", $user['customer_id'], time() + (30 * 24 * 60 * 60), "/");
        }
        echo json_encode(["success" => true, "message" => "Login erfolgreich!"]);
    } else {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Email oder Passwort falsch!"]);
    }
    exit;
}
?>
