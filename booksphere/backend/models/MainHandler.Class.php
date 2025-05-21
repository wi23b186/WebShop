<?php
class MainHandler {
    private $user;
    private $product;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->user = new User($pdo);
        $this->product = new Product($pdo);
    }

    // Einstiegspunkt: verarbeitet GET- und POST-Anfragen
    public function handle() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_REQUEST['action'] ?? '';

        if ($method === 'POST') {
            $this->handlePost($action);
        } elseif ($method === 'GET') {
            $this->handleGet($action);
        }
    }

    // POST-Anfragen: Registrierung und Login
    private function handlePost($action) {
        if ($action === 'register') {
            $result = $this->user->register($_POST);
            if ($result['success']) {
                header('Location: ../../frontend/login.html');
            } else {
                echo "Registration failed! <a href='../../frontend/register.html'>Try again</a>";
            }
        } elseif ($action === 'login') {
            $remember = isset($_POST['remember']);
            $success = $this->user->login($_POST['identifier'], $_POST['password'], $remember);
            if ($success) {
                header('Location: ../../frontend/index.html');
            } else {
                echo "Login failed! <a href='../../frontend/login.html'>Try again</a>";
            }
        }
    }

    // GET-Anfragen: Produktsuche & Produktdetails
    private function handleGet($action) {
        if ($action === 'getProducts') {
            echo json_encode($this->product->getAllProducts());
        } elseif ($action === 'searchProducts') {
            $query = $_GET['query'] ?? '';
            $category = $_GET['category'] ?? '';
            $sql = "SELECT id, name, description, price, image FROM products WHERE 1=1";
            $params = [];

            if (!empty($query)) {
                $sql .= " AND (name LIKE ? OR description LIKE ?)";
                $params[] = "%$query%";
                $params[] = "%$query%";
            }

            if (!empty($category)) {
                $sql .= " AND category = ?";
                $params[] = $category;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } elseif ($action === 'getProductById' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        }
    }
}
