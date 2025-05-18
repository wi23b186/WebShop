
<?php
class Product {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
 public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllProducts() {
        $stmt = $this->pdo->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
       public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
  public function create($name, $description, $price, $category, $rating, $image) {
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category, rating, image) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $description, $price, $category, $rating, $image]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateField($id, $field, $value) {
        $allowed = ['name', 'description', 'price', 'category', 'rating'];
        if (!in_array($field, $allowed)) {
            return [false, 'Ungültiges Feld'];
        }

        $sql = "UPDATE products SET `$field` = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([$value, $id]);

        if (!$success) {
            $error = $stmt->errorInfo();
            return [false, $error[2]];
        }

        return [true, null];
    }
}
?>
