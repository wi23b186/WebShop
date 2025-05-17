<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($data) {
        if ($data['password'] !== $data['password_confirm']) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (salutation, firstname, lastname, address, postalcode, city, email, username, password, payment_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([
            $data['salutation'],
            $data['firstname'],
            $data['lastname'],
            $data['address'],
            $data['postalcode'],
            $data['city'],
            $data['email'],
            $data['username'],
            $hash,
            $data['payment_info']
        ]);
        if ($success) {
            return ['success' => true, 'message' => 'Registration successful!'];
        } else {
            return ['success' => false, 'message' => 'Registration failed!'];
        }
    }

    public function login($identifier, $password, $remember = false) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            if ($remember) {
                setcookie('remember_user', $user['id'], time() + (86400 * 30), "/"); // 30 Tage
            }

            return true;
        }
        return false;
    }

  public function update($id, $data) {
        $sql = "UPDATE users SET salutation = ?, firstname = ?, lastname = ?, address = ?, postalcode = ?, city = ?, email = ?, username = ?, payment_info = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['salutation'],
            $data['firstname'],
            $data['lastname'],
            $data['address'],
            $data['postalcode'],
            $data['city'],
            $data['email'],
            $data['username'],
            $data['payment_info'],
            $id
        ]);
    }
}
?>
