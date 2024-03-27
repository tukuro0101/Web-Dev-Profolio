<?php
require 'admin_safety.php';
include 'admin_panel_handle.php';

$productsPerPage = 5;

// Calculate the current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure the page is at least 1

// Calculate the offset for the query
$offset = ($page - 1) * $productsPerPage;

// Prepare the SQL query to fetch products with a limit and offset
$stmt = $pdo->prepare("SELECT * FROM Anime_Figures ORDER BY figure_id ASC LIMIT :offset, :productsPerPage");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':productsPerPage', $productsPerPage, PDO::PARAM_INT);
$stmt->execute();

// Fetch the products
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total number of pages
$totalProductsStmt = $pdo->query("SELECT COUNT(*) FROM Anime_Figures");
$totalProducts = $totalProductsStmt->fetchColumn();
$totalPages = ceil($totalProducts / $productsPerPage);

    // Fetch all categories for the dropdowns
    $allCategories = getAllCategories($pdo); // Ensure this function is defined and returns categories

// Fetch product for editing if an edit_product_id is present
$productToEdit = null;
if (isset($_GET['edit_product_id'])) {
    $editProductId = $_GET['edit_product_id'];
    $stmt = $pdo->prepare("SELECT * FROM Anime_Figures WHERE figure_id = ?");
    $stmt->execute([$editProductId]);
    $productToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <div class="page_container">
        <header><?php include 'nav.php'; ?></header>
        <main>
            <h1>Admin Panel</h1>
            <section class="product-management">
                <h2>Insert New Product</h2>
                <form action="admin_panel.php" method="post">
                    <input type="text" name="name" placeholder="Product Name" required>
                    <select name="category_id" required>
                        <?php foreach ($allCategories as $category): ?>
                            <option value="<?= htmlspecialchars($category['category_id']) ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="character" placeholder="Character" required>
                    <input type="number" step="0.01" name="price" placeholder="Price" required>
                    <textarea name="description" placeholder="Description" required></textarea>
                    <input type="text" name="image_url" placeholder="Image URL" required>
                    <button type="submit" name="insert_product">Insert Product</button>
                </form>
            </section>
            <?php if (isset($productToEdit)): ?>
                <section class="update-delete-product">
                    <h2>Update Product</h2>
                    <form action="admin_panel.php" method="post">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($productToEdit['figure_id']) ?>">
                        <input type="text" name="name" placeholder="Product Name" required value="<?= htmlspecialchars($productToEdit['name']) ?>">
                        <select name="category_id" required>
                            <?php foreach ($allCategories as $category): ?>
                                <option value="<?= htmlspecialchars($category['category_id']) ?>" <?= (isset($productToEdit) && $productToEdit['category_id'] == $category['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="character" placeholder="Character" required value="<?= htmlspecialchars($productToEdit['character']) ?>">
                        <input type="number" step="0.01" name="price" placeholder="Price" required value="<?= htmlspecialchars($productToEdit['price']) ?>">
                        <textarea name="description" placeholder="Description" required><?= htmlspecialchars($productToEdit['description']) ?></textarea>
                        <input type="text" name="image_url" placeholder="Image URL" required value="<?= htmlspecialchars($productToEdit['image_url']) ?>">
                        <button type="submit" name="update_product">Update Product</button>
                        <button type="submit" name="delete_product">Delete Product</button>
                    </form>
                </section>
            <?php endif; ?>
            <section class="category-management">
                <h2>Manage Categories</h2>
                
                <!-- Insert New Category Form -->
                <form action="admin_panel.php" method="post">
                    <input type="text" name="category_name" placeholder="Category Name" required>
                    <button type="submit" name="insert_category">Insert Category</button>
                </form>

                <form action="admin_panel.php" method="post">
                    <select name="category_id">
                        <?php foreach ($allCategories as $category): ?>
                            <option value="<?= htmlspecialchars($category['category_id']) ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="edit_category">Edit Category</button>
                </form>

                <?php if (isset($categoryToEdit)): ?>
                    <h2>Edit Category</h2>
    <form action="admin_panel.php" method="post">
        <input type="hidden" name="category_id" value="<?= htmlspecialchars($categoryToEdit['category_id']) ?>">
        <input type="text" name="category_name" placeholder="Category Name" required value="<?= htmlspecialchars($categoryToEdit['name']) ?>">
        <button type="submit" name="update_category">Update Category</button>
        <button type="submit" name="delete_category" onclick="return confirm('Are you sure you want to delete this category?');">Delete Category</button>
    </form>
                <?php endif; ?>
            </section>

            <section class="product-display">
                <h2>Products</h2>
                <?php foreach ($products as $product): ?>
                <div class="product">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <!-- Product details here -->
                    <a href="?edit_product_id=<?= $product['figure_id'] ?>">Edit</a>
                </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </section>
        </main>
        <footer><?php include 'contact.php'; ?></footer>
    </div>  


    <script>
function validateEditCategory() {
    var selectedCategory = document.querySelector('select[name="category_id"]').value;
    if (!selectedCategory) {
        alert("Please select a category.");
        return false; 
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    var updateCategoryBtn = document.getElementById('updateCategoryBtn');
    var selectCategory = document.querySelector('select[name="category_id"]');

    selectCategory.addEventListener('change', function() {
        if (selectCategory.value === "") {
            updateCategoryBtn.disabled = true; // Disable the button
        } else {
            updateCategoryBtn.disabled = false; // Enable the button
        }
    });

    if (selectCategory.value === "") {
        updateCategoryBtn.disabled = true; // Disable the button
    }
});



</script>
</body>
</html>

