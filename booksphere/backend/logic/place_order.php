<?php
session_start();
require_once '../config/dbaccess.php';
require_once '../models/Order.class.php';
require_once '../models/Product.class.php';
require_once '../models/Voucher.class.php';


if (!isset($_SESSION["user"])) {
    http_response_code(401);
    exit("Nicht eingeloggt.");
}

$stmt = $pdo->prepare("SELECT firstname, lastname, address, postalcode, city, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Pflichtfelder prüfen
$requiredFields = ['firstname', 'lastname', 'address', 'postalcode', 'city', 'email'];
$missing = [];

foreach ($requiredFields as $field) {
    if (empty($user[$field])) {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    http_response_code(400);
    echo "Bestellung nicht möglich. Fehlende Profilangaben: " . implode(', ', $missing);
    exit;
}

$userId = $_SESSION['user']['id'];
//$paymentMethod = $_POST["payment_info"] ?? '';
$voucherCode = $_POST["voucher"] ?? '';

//if (empty($paymentMethod)) {
//    http_response_code(400);
 //   exit("Zahlungsmethode fehlt.");
//}

// Initialisiere Models
$dbObj = new DBAccess();
$pdo = $dbObj->pdo;

$productModel = new Product($pdo);
$orderModel = new Order($pdo);
$voucherModel = new Voucher($pdo);
$cart = $_SESSION['cart'] ?? [];

$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart)) {
    http_response_code(400);
    exit("Warenkorb ist leer.");
}

// Berechne Gesamtpreis & baue Item-Liste für Order-Klasse
$total = 0;
$orderItems = [];
foreach ($cart as $productId => $quantity) {
    $product = $productModel->getById($productId);
    if (!$product) continue;

    $price = $product['price'];
    $subtotal = $price * $quantity;
    $total += $subtotal;

    $orderItems[] = [
        'product_id' => $productId,
        'quantity' => $quantity,
        'price' => $price
    ];
}

$appliedDiscount = 0;
$validVoucherCode = null;

// Verarbeite Gutschein
if (!empty($voucherCode)) {
    $voucher = $voucherModel->validate($voucherCode);
    if ($voucher) {
        $available = $voucher['value'] - $voucher['used_value'];
        if ($available > 0) {
            $appliedDiscount = min($available, $total);
            $total -= $appliedDiscount;

            // ✅ Speicher den verbrauchten Teil
            $voucherModel->apply($voucher['id'], $appliedDiscount);

            $validVoucherCode = $voucher['code'];
        }
    } else {
        http_response_code(400);
        exit("Ungültiger oder abgelaufener Gutscheincode.");
    }
}

// Bestellung speichern
try {
    $orderId = $orderModel->create($userId, $orderItems, $total, $validVoucherCode);

    // Warenkorb leeren
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);

    echo "Bestellung erfolgreich! (Bestellnummer: #$orderId)";
} catch (Exception $e) {
    http_response_code(500);
    echo "Fehler beim Speichern der Bestellung: " . $e->getMessage();
}
?>
