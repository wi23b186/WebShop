<?php
require_once __DIR__ . '/init.php';

header('Content-Type: application/json; charset=utf-8');

$role = $_SESSION['role'] ?? 'guest';
$username = $_SESSION['username'] ?? '';
$loggedIn = isset($_SESSION['user_id']); // wichtig!

echo json_encode([
  "role" => $role,
  "username" => $username,
  "loggedIn" => $loggedIn // jetzt vorhanden!
]);
exit;
