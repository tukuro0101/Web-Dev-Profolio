<?php
require 'connection.php'; // Adjust the path as necessary

// Define the number of products per page
$productsPerPage = 10;

// Determine which page number visitor is currently on
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure $page is at least 1

// Calculate the offset for the query
$offset = ($page - 1) * $productsPerPage;

// Prepare the SQL statement to fetch products with a limit and offset
$stmt = $pdo->prepare("SELECT * FROM Anime_Figure ORDER BY Figure_ID LIMIT :offset, :productsPerPage");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':productsPerPage', $productsPerPage, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Get the total number of products to calculate the total number of pages
$totalProductsStmt = $pdo->query("SELECT COUNT(*) FROM Anime_Figure");
$totalProducts = $totalProductsStmt->fetchColumn();
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
                <!-- Category Sidebar Content Here -->
            </aside>

            <section class="product-listing">
                <?php foreach ($products as $product): ?>
                    <div class="product">
                        <h3><?= htmlspecialchars($product['Name']) ?></h3>
                        <p><?= htmlspecialchars($product['Category']) ?></p>
                        <!-- Add other product details here -->
                    </div>
                <?php endforeach; ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>"<?= $i === $page ? ' class="active"' : '' ?>><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </section>
        </main>

        <footer><?php include 'contact.php'; ?></footer>
    </div>
</body>
</html>
