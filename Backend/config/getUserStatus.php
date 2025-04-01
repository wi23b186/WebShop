<?php
// php/getUserStatus.php
//session_start();
// Falls du Remember-Me etc. hast, kommt hier session_init.php
require_once __DIR__ . '/init.php';

header('Content-Type: application/json; charset=utf-8');

// Standard: Gast
$role = $_SESSION['role'] ?? 'guest';
$username = $_SESSION['username'] ?? '';

echo json_encode([
  "role" => $role,
  "username" => $username
]);
exit;
