<?php
require 'connection.php'; // Database connection setup

$searchTerm = $_GET['term'] ?? '';
$results = [];

if ($searchTerm) {
    $stmt = $pdo->prepare("SELECT figure_id, name FROM anime_figures WHERE name LIKE :term LIMIT 10");
    $stmt->execute(['term' => '%' . $searchTerm . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode($results);