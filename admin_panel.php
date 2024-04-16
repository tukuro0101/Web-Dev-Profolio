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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
        <header><?php include 'nav.php'; ?></header>
        <main>
        <h1>Admin Panel</h1>
            <section class="product-management">
                <h2>Insert New Product</h2>
                <form action="admin_panel_handle.php" method="post" enctype="multipart/form-data">
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
                    <!-- Image upload or URL input -->
                    <div id="imageUploadOptionsInsert">
                        <label for="imageFile">Upload Image File:</label>
                        <input type="file" name="imageFile" id="imageFileInsert" accept="image/png, image/jpeg, image/gif">
                    </div>
                    <div id="imageUrlInputInsert">
                        <label for="imageUrl">Image URL:</label>
                        <input type="text" name="imageUrl" id="imageUrlInsert">
                    </div>

                    <!-- Button for toggling between upload and URL input -->
                    <button type="button" id="toggleImageInputInsert">Toggle Image Input</button>

                    <!-- Submit button -->
                    <button type="submit" name="insert_product">Insert Product</button>
                </form>
            </section>
            <?php if (isset($productToEdit)): ?>
                <section class="update-delete-product">
                    <h2>Update Product</h2>
                    <div>
                        <h3>Current Image:</h3>
                        <?php if ($productToEdit['image_url']): ?>
                            <img src="<?= htmlspecialchars($productToEdit['image_url']) ?>" alt="Current Image" class="product-image">
                        <?php else: ?>
                            <p>No image available</p>
                        <?php endif; ?>
                    </div>
                    <form action="admin_panel_handle.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($productToEdit['figure_id']) ?>">
                        <input type="text" name="name" placeholder="Product Name" required value="<?= htmlspecialchars($productToEdit['name']) ?>">
                        <select name="category_id" required>
                            <?php foreach ($allCategories as $category): ?>
                                <option value="<?= htmlspecialchars($category['category_id']) ?>" <?= ($productToEdit['category_id'] == $category['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="character" placeholder="Character" required value="<?= htmlspecialchars($productToEdit['character']) ?>">
                        <input type="number" step="0.01" name="price" placeholder="Price" required value="<?= htmlspecialchars($productToEdit['price']) ?>">
                        <textarea name="description" placeholder="Description" required><?= htmlspecialchars($productToEdit['description']) ?></textarea>
                        <!-- Image upload or URL input -->
                        <div id="imageUploadOptionsUpdate">
                            <label for="imageFileUpdate">Upload New Image File:</label>
                            <input type="file" name="imageFile" id="imageFileUpdate" accept="image/png, image/jpeg, image/gif">
                        </div>
                        <div id="imageUrlInputUpdate">
                            <label for="imageUrlUpdate">Image URL:</label>
                            <input type="text" name="imageUrlUpdate" id="imageUrlUpdate">
                        </div>

                        <!-- Button for toggling between upload and URL input -->
                        <button type="button" id="toggleImageInputUpdate">Toggle Image Input</button>

                         <!-- Select button to remove image -->
                         <?php if ($productToEdit['image_url']): ?>
                            <input type="checkbox" name="delete_image" id="delete_image">
                            <label for="delete_image">Delete Image</label>
                        <?php endif; ?>

                        <!-- Submit buttons -->
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
                        <?php if ($product['image_url']): ?>
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Product Image" class="product-image" width="100px" height="100px">
                        <?php else: ?>
                            <p>No image available</p>
                        <?php endif; ?>
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

            <?php include 'user_control.php'; ?>
        </main>
        <footer><?php include 'contact.php'; ?></footer>
    </div>

    <script>
   document.addEventListener("DOMContentLoaded", function() {
    // Get references to the image upload and URL input elements for insert new product form
    var imageUploadOptionsInsert = document.getElementById("imageUploadOptionsInsert");
    var imageUrlInputInsert = document.getElementById("imageUrlInputInsert");

    // Get reference to the toggle button for insert new product form
    var toggleImageInputButtonInsert = document.getElementById("toggleImageInputInsert");

    // Add click event listener to the toggle button for insert new product form
    toggleImageInputButtonInsert.addEventListener("click", function() {
        // Toggle display property of image upload and URL input elements for insert new product form
        if (imageUploadOptionsInsert.style.display === "none") {
            imageUploadOptionsInsert.style.display = "block";
            imageUrlInputInsert.style.display = "none";
        } else {
            imageUploadOptionsInsert.style.display = "none";
            imageUrlInputInsert.style.display = "block";
        }
    });

    // Get references to the image upload and URL input elements for update/edit product form
    var imageUploadOptionsUpdate = document.getElementById("imageUploadOptionsUpdate");
    var imageUrlInputUpdate = document.getElementById("imageUrlInputUpdate");

    // Get reference to the toggle button for update/edit product form
    var toggleImageInputButtonUpdate = document.getElementById("toggleImageInputUpdate");

    // Add click event listener to the toggle button for update/edit product form
    toggleImageInputButtonUpdate.addEventListener("click", function() {
        // Toggle display property of image upload and URL input elements for update/edit product form
        if (imageUploadOptionsUpdate.style.display === "none") {
            imageUploadOptionsUpdate.style.display = "block";
            imageUrlInputUpdate.style.display = "none";
        } else {
            imageUploadOptionsUpdate.style.display = "none";
            imageUrlInputUpdate.style.display = "block";
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // Add event listener to handle remove image button clicks
    const removeImageButtons = document.querySelectorAll('.remove-image-btn');
    removeImageButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            removeImage(productId);
        });
    });

    // Update the JavaScript function to handle remove image request with product ID
    function removeImage(productId) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'admin_panel_handle.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Image removed successfully, update UI as needed
                console.log(xhr.responseText);
                // You may need to reload the page or update the UI here
            } else {
                // Error handling
                console.error('Error removing image:', xhr.responseText);
            }
        }
    };

    // Get the remove image checkbox state
    const removeImageCheckbox = document.getElementById('delete_image');
    const removeImageChecked = removeImageCheckbox.checked ? 1 : 0; // Convert boolean to 1 or 0

    // Send request with product ID and remove image flag
    xhr.send(`remove_image=1&product_id=${productId}&remove_image_checked=${removeImageChecked}`);
}

});

</script>
</body>
</html>
