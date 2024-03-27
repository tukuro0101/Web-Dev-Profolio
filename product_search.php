<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'connection.php'; // Adjust the path as needed

$results = [];

if (isset($_GET['searchQuery']) && $_GET['searchQuery'] !== '') {
    // Echo the search query parameter for debugging
    echo "Search Query: " . $_GET['searchQuery'] . "<br>";

    // Trim the search term and replace consecutive spaces with a single space
    $searchTermRaw = trim($_GET['searchQuery']);
    $searchTermProcessed = preg_replace('/\s+/', ' ', $searchTermRaw);

    // Decode URL encoded characters
    $searchTermProcessed = urldecode($searchTermProcessed);

    $likeTerm = '%' . $searchTermProcessed . '%';

    try {
        // Prepare the SQL statement using named placeholders
        $sql = "SELECT figure_id, name FROM anime_figures 
        WHERE TRIM(name) LIKE :likeTerm 
        OR TRIM(character) LIKE :likeTerm";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind the like term to the named placeholders
        $stmt->bindValue(':likeTerm', $likeTerm);

        // Execute the query
        $stmt->execute();

        // Fetch the results as an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle database errors
        $results = ['error' => 'Database error: ' . $e->getMessage()];
    }
} else {
    $results = ['error' => 'No search term provided'];
}

// Output results as JSON
header('Content-Type: application/json');
echo json_encode($results);
?>
