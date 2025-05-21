<?php
class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Neue Bestellung mit Produkten (und optional Gutschein) anlegen
    public function create($userId, $items, $total, $voucherCode = null) {
        $this->pdo->beginTransaction();

        if ($voucherCode !== null) {
            $stmt = $this->pdo->prepare("
                INSERT INTO orders (user_id, total, order_date, voucher_code)
                VALUES (?, ?, NOW(), ?)
            ");
            $stmt->execute([$userId, $total, $voucherCode]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO orders (user_id, total, order_date)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$userId, $total]);
        }

        $orderId = $this->pdo->lastInsertId();

        $stmtItem = $this->pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($items as $item) {
            $stmtItem->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
        }

        $this->pdo->commit();
        return $orderId;
    }

    // Alle Bestellungen eines Nutzers inkl. Artikel abrufen
    public function getOrdersByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date ASC");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            $itemStmt = $this->pdo->prepare("
                SELECT i.id, p.name, i.quantity, i.price
                FROM order_items i
                JOIN products p ON i.product_id = p.id
                WHERE i.order_id = ?
            ");
            $itemStmt->execute([$order['id']]);
            $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $orders;
    }

    // Alle Daten für eine Rechnung abrufen
    public function getOrderDetailsForInvoice($orderId, $userId) {
        $stmt = $this->pdo->prepare("
            SELECT o.*, u.firstname, u.lastname, u.address, u.city, u.postalcode
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ? AND o.user_id = ?
        ");
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) return null;

        $itemStmt = $this->pdo->prepare("
            SELECT p.name, i.quantity, i.price
            FROM order_items i
            JOIN products p ON i.product_id = p.id
            WHERE i.order_id = ?
        ");
        $itemStmt->execute([$orderId]);
        $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }
    // Einzelnes Produkt aus Bestellung entfernen und Gesamtpreis aktualisieren
public function removeOrderItemAndUpdateTotal($itemId) {
    // Hole order_id
    $stmt = $this->pdo->prepare("SELECT order_id FROM order_items WHERE id = ?");
    $stmt->execute([$itemId]);
    $orderId = $stmt->fetchColumn();

    if (!$orderId) {
        return ['success' => false, 'message' => 'Order-ID nicht gefunden'];
    }

    // Lösche das Produkt aus order_items
    $stmt = $this->pdo->prepare("DELETE FROM order_items WHERE id = ?");
    $success = $stmt->execute([$itemId]);

    if ($success) {
        // Neue Gesamtsumme berechnen
        $stmt = $this->pdo->prepare("SELECT SUM(price * quantity) FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $newTotal = $stmt->fetchColumn();
        if ($newTotal === null) $newTotal = 0;

        // Update orders.total
        $stmt = $this->pdo->prepare("UPDATE orders SET total = ? WHERE id = ?");
        $stmt->execute([$newTotal, $orderId]);

        return ['success' => true, 'newTotal' => $newTotal];
    } else {
        return ['success' => false, 'message' => 'Löschen fehlgeschlagen'];
    }
}


}
?>
