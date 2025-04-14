<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json;charset=utf-8");

require_once("dataHandler.php");

try {
  $stmt = $pdo->query("SELECT * FROM books");
  $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($books);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => $e->getMessage()]);
}
?>
