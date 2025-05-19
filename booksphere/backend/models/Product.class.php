
<?php
class Product {
    private $pdo;
    private $uploadDir;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    $this->uploadDir = __DIR__ . '/../productpictures/';
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
 public function create($data, $imageFile) {
        // Pflichtfelder prüfen
        $required = ['name', 'description', 'price', 'category', 'rating'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return [false, 'Alle Felder sind Pflicht.'];
            }
        }

        // Bild speichern
        if (!isset($imageFile['tmp_name']) || !is_uploaded_file($imageFile['tmp_name'])) {
            return [false, 'Bild fehlt oder konnte nicht hochgeladen werden.'];
        }

        $filename = uniqid() . '_' . basename($imageFile['name']);
        $targetPath = $this->uploadDir . $filename;

        if (!move_uploaded_file($imageFile['tmp_name'], $targetPath)) {
            return [false, 'Fehler beim Hochladen des Bildes.'];
        }

        // In Datenbank einfügen
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, description, price, category, rating, image)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $success = $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['category'],
            $data['rating'],
            $filename
        ]);

        return [$success, $success ? null : 'Fehler beim Speichern in der Datenbank.'];
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
