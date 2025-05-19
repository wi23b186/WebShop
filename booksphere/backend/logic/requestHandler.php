<?php
session_start();
require_once '../config/dbaccess.php';
require_once '../models/User.class.php';
require_once '../models/Product.class.php';
require_once '../models/MainHandler.class.php';  

$db = new DBAccess();
$handler = new MainHandler($db->pdo);
$handler->handle();  
