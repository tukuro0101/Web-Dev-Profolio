<?php
require 'connection.php'; // Adjust the path as necessary

// Define constants and initial variables
$productsPerPage = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $productsPerPage;
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : null;
$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : null;
$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'figure_id_asc';

// Fetch categories for the sidebar
$categoriesStmt = $pdo->query("SELECT category_id, name FROM categories ORDER BY name");
$categories = $categoriesStmt ? $categoriesStmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Initialize SQL query
$sql = "SELECT af.*, c.name AS category_name FROM anime_figures af LEFT JOIN categories c ON af.category_id = c.category_id";

// Initialize parameters array
$params = [];

// Add conditions for category filter and search query
if (!empty($categoryFilter)) {
    $sql .= " WHERE af.category_id = :categoryFilter";
    $params[':categoryFilter'] = $categoryFilter;
}

if (!empty($searchQuery)) {
    $sql .= (isset($params[':categoryFilter']) ? " AND " : " WHERE ") . "af.Name LIKE :searchQuery";
    $params[':searchQuery'] = "%$searchQuery%";
}

// Apply sorting option
$sql .= match ($sortOption) {
    'price_asc' => " ORDER BY Price ASC",
    'price_desc' => " ORDER BY Price DESC",
    'name_asc' => " ORDER BY Name ASC",
    'name_desc' => " ORDER BY Name DESC",
    'date_added_desc' => " ORDER BY Date_Added DESC",
    default => " ORDER BY Figure_ID ASC",
};

// Append LIMIT clause
$sql .= " LIMIT :offset, :productsPerPage";
$params[':offset'] = $offset;
$params[':productsPerPage'] = $productsPerPage;

// Prepare and execute the SQL statement
$stmt = $pdo->prepare($sql);
foreach ($params as $key => &$value) {
    $stmt->bindParam($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total pages for pagination
$totalSql = "SELECT COUNT(*) FROM anime_figures af";
if (!empty($categoryFilter)) {
    $totalSql .= " WHERE af.category_id = :categoryFilter";
    // For simplicity, not considering the search query in the total count. Adjust if needed.
}
$totalStmt = $pdo->prepare($totalSql);
if (!empty($categoryFilter)) {
    $totalStmt->bindParam(':categoryFilter', $params[':categoryFilter'], PDO::PARAM_STR);
}
$totalStmt->execute();
$totalProducts = $totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $productsPerPage);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'style.php'; ?>
    <title>Product Listing</title>
</head>
<body>
    <div class="page_container">
        <header><?php include 'nav.php'; ?></header>
        
        <main style="display: flex;">
        <aside class="category-sidebar">
    <h2>Categories</h2>
    <ul>
        <li> <a href="main.php">ALL</a></li>
        <?php foreach ($categories as $category): ?>
            <li>
                <!-- Use 'category' instead of 'category_id' -->
                <a href="?category=<?= urlencode($category['category_id']) ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>

<form id="sortForm" action="" method="get">
    <select name="sort" id="sortSelect" onchange="this.form.submit();">
        <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Price Low to High</option>
        <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Price High to Low</option>
        <option value="date_added_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_added_desc') ? 'selected' : '' ?>>Added Recently</option>
        <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>Alphabetical</option>
        <option value="name_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : '' ?>>Reversed Alphabetical</option>
    </select>
    <!-- Retain the search query -->
    <?php if (isset($_GET['searchQuery'])): ?>
        <input type="hidden" name="searchQuery" value="<?= htmlspecialchars($_GET['searchQuery']) ?>">
    <?php endif; ?>
    <!-- Retain the category filter -->
    <?php if (isset($_GET['category'])): ?>
        <input type="hidden" name="category" value="<?= htmlspecialchars($_GET['category']) ?>">
    <?php endif; ?>
</form>

<section class="product-listing">
<?php foreach ($products as $product): ?>
    <div class="product">
        <h2><b><?= htmlspecialchars($product['name']) ?></b></h2>
        <h1>$<?= htmlspecialchars($product['price']) ?></h1>
        <!-- Make the product image clickable -->
        <a href="product_view.php?id=<?= $product['figure_id'] ?>">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Image of <?= htmlspecialchars($product['name']) ?>" width="300" height="300">
        </a>
        <h1><b><a href="product_view.php?id=<?= $product['figure_id'] ?>"><?= htmlspecialchars($product['name']) ?></a></b></h1>
        <!-- Edit Link for admins -->
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
            <a href="admin_panel.php?edit=<?= $product['figure_id'] ?>">Edit</a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

</section>

<!-- Pagination Links -->
<div class="pagination">
    <?php if ($totalPages > 1): ?>
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?><?php if ($categoryFilter) echo '&category=' . urlencode($categoryFilter); ?><?php if ($sortOption) echo '&sort=' . $sortOption; ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?><?php if ($categoryFilter) echo '&category=' . urlencode($categoryFilter); ?><?php if ($sortOption) echo '&sort=' . $sortOption; ?>" <?= ($page == $i) ? 'class="active"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?><?php if ($categoryFilter) echo '&category=' . urlencode($categoryFilter); ?><?php if ($sortOption) echo '&sort=' . $sortOption; ?>">Next</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

</main>

<footer><?php include 'contact.php';?></footer>
</div>


</body>
<script>
document.getElementById('sortSelect').addEventListener('change', function() {
    this.form.submit();
});

</script>
</html>

