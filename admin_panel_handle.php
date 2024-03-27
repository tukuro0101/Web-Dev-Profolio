<?php
include 'connection.php';

// Function to fetch all categories
function getAllCategories($pdo) {
    $stmt = $pdo->query("SELECT category_id, name FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch all categories
$allCategories = getAllCategories($pdo);

// Handle category actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['edit_category'])) {
        $editCategoryId = $_POST['category_id'];
        $editCategoryStmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
        $editCategoryStmt->execute([$editCategoryId]);
        $categoryToEdit = $editCategoryStmt->fetch(PDO::FETCH_ASSOC);
    } elseif (isset($_POST['update_category'])) {
        $categoryId = $_POST['category_id'];
        $categoryName = $_POST['category_name'];

        $updateCategoryStmt = $pdo->prepare("UPDATE categories SET name = ? WHERE category_id = ?");
        $result = $updateCategoryStmt->execute([$categoryName, $categoryId]);

        echo $result ? "Category updated successfully!" : "Error updating category.";

        // Redirect back to admin_panel.php
        header("Location: admin_panel.php");
        exit;
    } elseif (isset($_POST['delete_category'])) {
        $categoryId = $_POST['category_id'];
        $deleteCategoryStmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
        $result = $deleteCategoryStmt->execute([$categoryId]);

        echo $result ? "Category deleted successfully!" : "Error deleting category.";

        // Redirect back to admin_panel.php
        header("Location: admin_panel.php");
        exit;
    } elseif (isset($_POST['insert_category'])) {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $categoryName = $_POST['category_name'];
            $insertCategoryStmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $insertCategoryStmt->execute([$categoryName]);
            echo "New category inserted successfully!";

            // Redirect back to admin_panel.php
            header("Location: admin_panel.php");
            exit;
        } catch (PDOException $e) {
            die("Error inserting category: " . $e->getMessage());
        }
    }
}

// Example: Inserting a new product
if (isset($_POST['insert_product'])) {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $character = $_POST['character'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $imageURL = $_POST['image_url'];

    $stmt = $pdo->prepare("INSERT INTO Anime_Figures (`name`, `category_id`, `character`, `price`, `description`, `image_url`) VALUES (?, ?, ?, ?, ?, ?)");
    echo $stmt->execute([$name, $category_id, $character, $price, $description, $imageURL]) ? "New product inserted successfully!" : "Error inserting product.";

    // Redirect back to admin_panel.php
    header("Location: admin_panel.php");
    exit;
}

// Fetch the product for editing
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $editStmt = $pdo->prepare("SELECT * FROM Anime_Figures WHERE figure_id = ?");
    $editStmt->execute([$edit_id]);
    $productToEdit = $editStmt->fetch();
}


// Handle product update and deletion
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $character = $_POST['character'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $imageURL = $_POST['image_url'];

    $updateStmt = $pdo->prepare("UPDATE Anime_Figures SET `name` = ?, `category_id` = ?, `character` = ?, `price` = ?, `description` = ?, `image_url` = ? WHERE `figure_id` = ?");
    echo $updateStmt->execute([$name, $category_id, $character, $price, $description, $imageURL, $id]) ? "Product updated successfully!" : "Error updating product.";

    // Redirect back to admin_panel.php
    header("Location: admin_panel.php");
    exit;
} elseif (isset($_POST['delete_product'])) {
    $id = $_POST['id'];
    $deleteStmt = $pdo->prepare("DELETE FROM Anime_Figures WHERE figure_id = ?");
    echo $deleteStmt->execute([$id]) ? "Product deleted successfully!" : "Error deleting product.";

    // Redirect back to admin_panel.php
    header("Location: admin_panel.php");
    exit;
}
?>