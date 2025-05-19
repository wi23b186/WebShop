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
        if ($user && password_verify($password, $user['password']) && $user['active']) {
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

    public function updatePartial($id, $data) {
        if (empty($data)) {
            return false;
        }
        $columns = [];
        $values = [];
        foreach ($data as $key => $value) {
            $columns[] = "$key = ?";
            $values[] = $value;
        }
        $sql = "UPDATE users SET " . implode(', ', $columns) . " WHERE id = ?";
        $values[] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function autoLoginFromCookie() {
        if (!isset($_SESSION['user']) && isset($_COOKIE['remember_user'])) {
            $user = $this->getUserById($_COOKIE['remember_user']);
            if ($user) {
                $_SESSION['user'] = $user;
            }
        }
    }

    public function getSessionUserData() {
        if (isset($_SESSION['user'])) {
            return [
                'loggedIn' => true,
                'username' => $_SESSION['user']['username'],
                'role' => $_SESSION['user']['role']
            ];
        }
        return ['loggedIn' => false];
    }

    public function changePassword($id, $currentPassword, $newPassword) {
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Neues Passwort ist zu kurz (min. 6 Zeichen).'];
        }

        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($currentPassword, $row['password'])) {
            return ['success' => false, 'message' => 'Aktuelles Passwort ist falsch.'];
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$newHash, $id]);

        return ['success' => true];
    }

    public function getCurrentUserData() {
        if (!isset($_SESSION['user'])) {
            return ['loggedIn' => false];
        }

        $user = $_SESSION['user'];

        return [
            'loggedIn' => true,
            'role' => $user['role'],
            'salutation' => $user['salutation'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'address' => $user['address'],
            'postalcode' => $user['postalcode'],
            'city' => $user['city'],
            'email' => $user['email'],
            'payment_info' => $user['payment_info']
        ];
    }

    // ✅ Zusätzliche Funktionen für Admin-Kundenseite:
    public function getAllCustomers() {
        $stmt = $this->pdo->prepare("SELECT id, username, email, active FROM users WHERE role = 'customer'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setActive($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE users SET active = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
public function checkPassword($id, $password) {
    $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row && password_verify($password, $row['password']);
}

}
?>
