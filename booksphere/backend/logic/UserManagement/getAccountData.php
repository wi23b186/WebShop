<?php
session_start();
require_once '../../config/dbaccess.php';
require_once '../../models/User.class.php';

header('Content-Type: application/json; charset=utf-8');

$db = new DBAccess();
$user = new User($db->pdo);

// Cookie-Autologin
$user->autoLoginFromCookie();

// Session-Daten als JSON zurückgeben
echo json_encode($user->getCurrentUserData());
?>