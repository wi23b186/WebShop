<?php
header("Access-Control-Allow-Origin: *"); // Passe exakt an!
header("Content-Type: application/json;charset=utf-8");

// Datenbankverbindung
try {
  $pdo = new PDO("mysql:host=localhost;dbname=test;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);

  $stmt = $pdo->query("SELECT * FROM books");
  $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($books);

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => $e->getMessage()]);
}
