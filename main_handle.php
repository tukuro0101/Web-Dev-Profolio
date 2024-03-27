<?php
require 'connection.php';

$productsPerPage = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $productsPerPage;
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : null;
$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : null;
$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'figure_id_asc';

$categoriesStmt = $pdo->query("SELECT category_id, name FROM categories ORDER BY name");
$categories = $categoriesStmt ? $categoriesStmt->fetchAll(PDO::FETCH_ASSOC) : [];

$sql = "SELECT af.*, c.name AS category_name FROM anime_figures af LEFT JOIN categories c ON af.category_id = c.category_id";
$conditions = [];
$params = [];

if ($categoryFilter) {
    $conditions[] = "af.category_id = :categoryFilter";
    $params[':categoryFilter'] = $categoryFilter;
}
if ($searchQuery) {
    $conditions[] = "(af.name LIKE :searchQuery OR af.character LIKE :searchQueryCharacter)";
    $params[':searchQuery'] = "%{$searchQuery}%";
    $params[':searchQueryCharacter'] = "%{$searchQuery}%";
}

if ($conditions) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

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

$sql .= " LIMIT :offset, :productsPerPage";
$params[':offset'] = $offset;
$params[':productsPerPage'] = $productsPerPage;

$stmt = $pdo->prepare($sql);

foreach ($params as $key => &$val) {
    if ($key == ':offset' || $key == ':productsPerPage') {
        $stmt->bindValue($key, (int)$val, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $val);
    }
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalSql = "SELECT COUNT(*) FROM anime_figures af";
if ($conditions) {
    $totalSql .= " WHERE " . implode(' AND ', $conditions);
}

$totalStmt = $pdo->prepare($totalSql);

foreach ($params as $key => &$val) {
    // Exclude limit parameters
    if ($key != ':offset' && $key != ':productsPerPage') {
        $totalStmt->bindValue($key, $val);
    }
}

$totalStmt->execute();
$totalProducts = (int)$totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $productsPerPage);
?>
