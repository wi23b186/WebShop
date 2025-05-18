<?php
session_start();
require_once '../../config/dbaccess.php';
require_once '../../models/Order.class.php';
require_once '../../models/Product.class.php';
require_once '../../models/Voucher.class.php';

if (!isset($_SESSION["user"])) {
    http_response_code(401);
    exit("Nicht eingeloggt.");
}

$userId = $_SESSION['user']['id'];
$pdo = (new DBAccess())->pdo;

$orderModel = new Order($pdo);
$productModel = new Product($pdo);
$voucherModel = new Voucher($pdo);

// Benutzerprofil prüfen
$stmt = $pdo->prepare("SELECT firstname, lastname, address, postalcode, city, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$requiredFields = ['firstname', 'lastname', 'address', 'postalcode', 'city', 'email'];
$missing = array_filter($requiredFields, fn($field) => empty($user[$field]));

if (!empty($missing)) {
    http_response_code(400);
    echo "Bestellung nicht möglich. Fehlende Profilangaben: " . implode(', ', $missing);
    exit;
}

// Warenkorb laden
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    http_response_code(400);
    exit("Warenkorb ist leer.");
}

// Warenkorbpositionen verarbeiten
$total = 0;
$orderItems = [];
foreach ($cart as $productId => $quantity) {
    $product = $productModel->getById($productId);
    if (!$product) continue;

    $price = $product['price'];
    $orderItems[] = [
        'product_id' => $productId,
        'quantity' => $quantity,
        'price' => $price
    ];
    $total += $price * $quantity;
}

// Gutschein anwenden (wenn vorhanden)
$voucherCode = $_POST['voucher'] ?? '';
$validVoucherCode = null;
$appliedDiscount = 0;

if (!empty($voucherCode)) {
    $voucher = $voucherModel->validate($voucherCode);
    if ($voucher) {
        $available = $voucher['value'] - $voucher['used_value'];
        if ($available > 0) {
            $appliedDiscount = min($available, $total);
            $total -= $appliedDiscount;
            $voucherModel->apply($voucher['id'], $appliedDiscount);
            $validVoucherCode = $voucher['code'];
        }
    } else {
        http_response_code(400);
        exit("Ungültiger oder abgelaufener Gutscheincode.");
    }
}

// Bestellung anlegen
try {
    $orderId = $orderModel->create($userId, $orderItems, $total, $validVoucherCode);

    // Cart in DB oder Session löschen
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
    unset($_SESSION['cart']);

    echo "Bestellung erfolgreich! (Bestellnummer: #$orderId)";
} catch (Exception $e) {
    http_response_code(500);
    echo "Fehler beim Speichern der Bestellung: " . $e->getMessage();
}
?>
