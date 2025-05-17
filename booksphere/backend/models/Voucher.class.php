<?php
class Voucher {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

   public function validate($code) {
    $stmt = $this->pdo->prepare("
        SELECT * FROM vouchers 
        WHERE code = ? 
          AND expiry_date >= CURDATE()
    ");
    $stmt->execute([$code]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
   public function apply($id, $amount) {
    $stmt = $this->pdo->prepare("
        UPDATE vouchers 
        SET used_value = used_value + ? 
        WHERE id = ?
    ");
    $stmt->execute([$amount, $id]);
}
}
?>