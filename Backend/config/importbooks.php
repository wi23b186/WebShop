<?php

require_once __DIR__ . '/dataHandler.php';
//require_once '/init.php';

header("Access-Control-Allow-Origin: *"); // genau anpassen!
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json;charset=utf-8");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Fehler anzeigen für Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=test;charset=utf8", "root", "");

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        throw new Exception("Keine gültigen JSON-Daten empfangen.");
    }

    $stmt = $pdo->prepare("INSERT INTO books (title, author, genre, price, image, publishedDate, description, language) 
                           VALUES (?, ?, ?, ?, ?, ?, ?,?)");

    foreach ($data as $book) {
        $stmt->execute([
            $book['title'],
            $book['author'],
            $book['genre'],
            $book['price'],
            $book['image'],
            $book['publishedDate'],
            $book['description'],
            $book['language'] ?? 'unbekannt' // wichtig: Standardwert, falls JS nicht sendet!
        ]);
    }
   // console.log("Sende Bücher an Backend:", allBooks.length, allBooks);

    echo json_encode(["success" => true, "count" => count($data)]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
    console.log("Sende Bücher an Backend:", allBooks.length, allBooks);
}
