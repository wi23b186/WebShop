<?php
class Voucher {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT *, DATE_FORMAT(expiry_date, '%Y/%m/%d') as expiry_date FROM vouchers ORDER BY expiry_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($code, $value, $expiry_date) {
        if (empty($code)) {
            $code = strtoupper(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 5)), 0, 5));
        }

        $formattedDate = date('Y-m-d', strtotime($expiry_date));
        $stmt = $this->pdo->prepare("INSERT INTO vouchers (code, value, expiry_date) VALUES (?, ?, ?)");
        $stmt->execute([$code, $value, $formattedDate]);
        return $code;
    }


    public function validate($code) {
        $stmt = $this->pdo->prepare("SELECT * FROM vouchers WHERE code = ? AND expiry_date >= CURDATE()");
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function apply($id, $amount) {
        $stmt = $this->pdo->prepare("UPDATE vouchers SET used_value = used_value + ? WHERE id = ?");
        $stmt->execute([$amount, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM vouchers WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
