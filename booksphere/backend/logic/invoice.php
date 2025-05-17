<?php
require_once '../config/dbaccess.php';
require_once '../models/Order.class.php';
require_once __DIR__ . '/../vendor/fpdf186/fpdf.php';

session_start();

if (!isset($_SESSION['user'])) die("Nicht eingeloggt");
if (!isset($_GET['order_id'])) die("Keine Bestellung angegeben.");

$orderId = (int) $_GET['order_id'];
$userId = $_SESSION['user']['id'];

$db = new DBAccess();
$pdo = $db->pdo;

$orderModel = new Order($pdo);
$order = $orderModel->getOrderDetailsForInvoice($orderId, $userId);

if (!$order) die("Bestellung nicht gefunden oder Zugriff verweigert.");

$invoiceNumber = 'INV-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);

// ✅ Eigene FPDF-Klasse mit Footer
class PDF extends FPDF {
    function Footer() {
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 6, 'Booksphere dankt Ihnen für Ihren Einkauf!', 0, 1, 'C');
        $this->Cell(0, 6, 'Booksphere GmbH · Bücherstraße 12 · 1010 Wien · Österreich', 0, 1, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 30);

// === Logo & Titel ===
$pdf->Image(__DIR__ . '/../productpictures/booksphere-logo.png', 10, 10, 40);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Booksphere - Rechnung', 0, 1, 'C');
$pdf->Ln(30);

// === Rechnungsinfo ===
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 6, "Rechnungsnummer: $invoiceNumber", 0, 1);
$pdf->Cell(100, 6, "Datum: " . $order['order_date'], 0, 1);
$pdf->Ln(5);

// === Kundenanschrift ===
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 6, "Kunde:", 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 6, "{$order['firstname']} {$order['lastname']}", 0, 1);
$pdf->Cell(100, 6, "{$order['address']}", 0, 1);
$pdf->Cell(100, 6, "{$order['postalcode']} {$order['city']}", 0, 1);
$pdf->Ln(10);

// === Tabellenkopf ===
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(90, 8, 'Produkt', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Menge', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Einzelpreis', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Gesamt', 1, 1, 'C', true);

// === Tabelleneinträge ===
$pdf->SetFont('Arial', '', 12);
$total = 0;
foreach ($order['items'] as $item) {
    $subtotal = $item['price'] * $item['quantity'];
    $total += $subtotal;

    $pdf->Cell(90, 8, $item['name'], 1);
    $pdf->Cell(30, 8, $item['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 8, '€ ' . number_format($item['price'], 2), 1, 0, 'R');
    $pdf->Cell(40, 8, '€ ' . number_format($subtotal, 2), 1, 1, 'R');
}

$pdf->Ln(3);

// === Gutscheininfo ===
if (!empty($order['voucher_code'])) {
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->SetTextColor(50, 100, 180);
    $pdf->Cell(190, 6, "Gutschein verwendet: {$order['voucher_code']}", 0, 1);
    $pdf->SetTextColor(0); // Zurück zu Schwarz
    $pdf->Ln(2);
}

// === Gesamtsumme farbig abgesetzt ===
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetFillColor(220, 235, 255);
$pdf->Cell(150, 10, 'Gesamtsumme:', 1, 0, 'R', true);
$pdf->Cell(40, 10, '€ ' . number_format($order['total'], 2), 1, 1, 'R', true);
$pdf->Ln(10);

// ✅ Ausgabe
$pdf->Output("I", "Rechnung_$invoiceNumber.pdf");
