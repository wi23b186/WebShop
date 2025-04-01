<?php
// checkLoginStatus.php
header("Access-Control-Allow-Origin: http://localhost/ProjektWebshop/WebShop/Backend/config/checkLoginStatus.php");
 // EXAKT dein Frontend
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');
//session_start(); // oder 
require_once 'init.php';

// Jetzt prüfst du wie gehabt:
if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'loggedIn' => true,
        'username' => $_SESSION['username']
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
