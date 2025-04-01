<?php
// menu.php
require_once __DIR__ . '/inc/session_init.php';

// Falls du nur das Menü brauchst, brauchst du keinen weiteren Output als JSON.
header('Content-Type: application/json; charset=utf-8');

$role = $_SESSION['role'];
$menuItems = [];

// Gast (guest)
if ($role === 'guest') {
    $menuItems = [
        ["name" => "Home",       "url" => "index.html"],
        ["name" => "Produkte",   "url" => "products.html"],
        ["name" => "Warenkorb",  "url" => "cart.html"],
        ["name" => "Login",      "url" => "login.html"]
    ];
}
// Eingeloggter Nutzer (user)
elseif ($role === 'user') {
    $menuItems = [
        ["name" => "Home",       "url" => "index.html"],
        ["name" => "Produkte",   "url" => "products.html"],
        ["name" => "Mein Konto", "url" => "account.html"],
        ["name" => "Warenkorb",  "url" => "cart.html"],
        ["name" => "Logout",     "url" => "logout.php"]
    ];
}
// Administrator
elseif ($role === 'admin') {
    $menuItems = [
        ["name" => "Home",                 "url" => "index.html"],
        ["name" => "Produkte bearbeiten",  "url" => "admin_products.html"],
        ["name" => "Kunden bearbeiten",    "url" => "admin_customers.html"],
        ["name" => "Gutscheine verwalten", "url" => "admin_vouchers.html"],
        ["name" => "Logout",               "url" => "logout.php"]
    ];
}

echo json_encode($menuItems);
