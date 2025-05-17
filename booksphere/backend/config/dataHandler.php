
<?php
require_once 'dbaccess.php';
require_once '../models/Product.class.php';

$db = new DBAccess();
$product = new Product($db->pdo);

if ($_GET['action'] == 'getProducts') {
    echo json_encode($product->getAllProducts());
}
?>
