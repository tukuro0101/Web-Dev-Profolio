<?php
require 'connection.php';
session_start();
// Initialize variables
$productsPerPage = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : null;
$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : null;
$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'figure_id_asc';

// Check if an admin has adjusted the product count
if (isset($_GET['productCount']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
    $productsPerPage = max((int)$_GET['productCount'], 1); // Ensure the product count is at least 1
}

// Calculate offset for pagination
$offset = ($page - 1) * $productsPerPage;

// Fetch categories
$categoriesStmt = $pdo->query("SELECT category_id, name FROM categories ORDER BY name");
$categories = $categoriesStmt ? $categoriesStmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Build SQL query for products
$sql = "SELECT af.*, c.name AS category_name FROM anime_figures af LEFT JOIN categories c ON af.category_id = c.category_id";
$conditions = [];
$params = [];

// Apply category filter
if ($categoryFilter) {
    $conditions[] = "af.category_id = :categoryFilter";
    $params[':categoryFilter'] = $categoryFilter;
}

// Apply search query
if ($searchQuery) {
    $conditions[] = "(af.name LIKE :searchQuery OR af.character LIKE :searchQueryCharacter)";
    $params[':searchQuery'] = "%{$searchQuery}%";
    $params[':searchQueryCharacter'] = "%{$searchQuery}%";
}

// Construct WHERE clause
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

// Apply sorting
switch ($sortOption) {
    case 'price_asc':
        $sql .= " ORDER BY af.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY af.price DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY af.name ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY af.name DESC";
        break;
    case 'date_added_desc':
        $sql .= " ORDER BY af.date_added DESC";
        break;
    default:
        $sql .= " ORDER BY af.figure_id ASC";
        break;
}

// Add LIMIT and OFFSET for pagination
$sql .= " LIMIT :offset, :productsPerPage";
$params[':offset'] = $offset;
$params[':productsPerPage'] = $productsPerPage;

// Prepare and execute SQL query
$stmt = $pdo->prepare($sql);

foreach ($params as $key => &$val) {
    if ($key === ':offset' || $key === ':productsPerPage') {
        $stmt->bindValue($key, (int)$val, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $val);
    }
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total number of products for pagination
$totalSql = "SELECT COUNT(*) FROM anime_figures af";
if (!empty($conditions)) {
    $totalSql .= " WHERE " . implode(' AND ', $conditions);
}

// Prepare and execute total count query
$totalStmt = $pdo->prepare($totalSql);

foreach ($params as $key => &$val) {
    // Exclude limit parameters
    if ($key !== ':offset' && $key !== ':productsPerPage') {
        $totalStmt->bindValue($key, $val);
    }
}

$totalStmt->execute();
$totalProducts = (int)$totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $productsPerPage);

// If there are no products on the current page, redirect to the last page
if ($totalPages > 0 && $page > $totalPages) {
    header("Location: {$_SERVER['PHP_SELF']}?page={$totalPages}");
    exit;
}
?>
