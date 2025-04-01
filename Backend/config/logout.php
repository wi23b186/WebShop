<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

// Session leeren/destroyen
$_SESSION = [];
session_destroy();

// Eventuell "Remember Me"-Cookie löschen:
if (isset($_COOKIE['remember_me'])) {
    setcookie("remember_me", "", time() - 3600, "/");
}

echo json_encode([
    'success' => true,
    'message' => 'Logout erfolgreich!'
]);
