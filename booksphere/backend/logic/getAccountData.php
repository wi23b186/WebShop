<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user'])) {
    echo json_encode(['loggedIn' => false]);
    exit;
}

$user = $_SESSION['user'];

echo json_encode([
    'loggedIn' => true,
    'role' => $user['role'],
    'salutation' => $user['salutation'],
    'firstname' => $user['firstname'],
    'lastname' => $user['lastname'],
    'address' => $user['address'],
    'postalcode' => $user['postalcode'],
    'city' => $user['city'],
    'email' => $user['email'],
    'payment_info' => $user['payment_info']
]);
