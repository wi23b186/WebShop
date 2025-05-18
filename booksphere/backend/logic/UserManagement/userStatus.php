<?php
session_start();
require_once '../../config/dbaccess.php';
require_once '../../models/User.class.php';

$db = new DBAccess();
$user = new User($db->pdo);

// Automatisches Einloggen über Cookie, falls nötig
$user->autoLoginFromCookie();

// Sessiondaten zurückgeben
echo json_encode($user->getSessionUserData());
?>