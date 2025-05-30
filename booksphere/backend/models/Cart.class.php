<?php
require_once 'Product.class.php';

class Cart {
    private $pdo;
    private $productModel;
    private $sessionKey = 'cart';

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->productModel = new Product($pdo);

        // Warenkorb initialisieren, falls noch nicht vorhanden
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
    }

    // Ein Produkt zum Warenkorb hinzufügen (oder Menge erhöhen)
    public function add($productId) {
        $_SESSION[$this->sessionKey][$productId] = ($_SESSION[$this->sessionKey][$productId] ?? 0) + 1;
    }

    // Menge eines Produkts ändern oder entfernen
    public function updateQuantity($productId, $change) {
        if (!isset($_SESSION[$this->sessionKey][$productId])) return;

        switch ($change) {
            case 'increase':
                $_SESSION[$this->sessionKey][$productId]++;
                break;
            case 'decrease':
                $_SESSION[$this->sessionKey][$productId]--;
                if ($_SESSION[$this->sessionKey][$productId] <= 0) {
                    unset($_SESSION[$this->sessionKey][$productId]);
                }
                break;
            case 'remove':
                unset($_SESSION[$this->sessionKey][$productId]);
                break;
        }
    }

    // Gesamtanzahl aller Produkte im Warenkorb
    public function getCount() {
        return array_sum($_SESSION[$this->sessionKey]);
    }

    // Details aller Produkte im Warenkorb inkl. Menge laden
    public function getItems() {
        $items = [];
        foreach ($_SESSION[$this->sessionKey] as $id => $qty) {
            $product = $this->productModel->getById($id);
            if ($product) {
                $product['quantity'] = $qty;
                $items[] = $product;
            }
        }
        return $items;
    }
}
