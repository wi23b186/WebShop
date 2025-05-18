<?php
require_once '../../config/dbaccess.php';
require_once '../../models/Voucher.class.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
    exit;
}

$code = trim($_POST['code'] ?? ''); // darf leer sein, PHP generiert
$value = floatval($_POST['value'] ?? 0);
$date = $_POST['date'] ?? '';

// Validierung
if ($value <= 0) {
    echo json_encode(['success' => false, 'message' => 'Ungültiger Wert']);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['success' => false, 'message' => 'Ungültiges Datum']);
    exit;
}

// Gutschein erzeugen
try {
    $db = new DBAccess();
    $voucher = new Voucher($db->pdo);

    $generatedCode = $voucher->create($code, $value, $date);

    echo json_encode([
        'success' => true,
        'message' => 'Gutschein erstellt',
        'code' => $generatedCode
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Fehler: ' . $e->getMessage()
    ]);
}
?>
