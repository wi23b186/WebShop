<?php
class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

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
}
?>
