<?php
// dataHandler.php
$host = 'localhost';
$dbname = 'test';
$username_db = 'root';
$password_db = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Verbindung fehlgeschlagen: " . $e->getMessage());
}
?>
