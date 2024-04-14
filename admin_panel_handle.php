<?php
session_start();
include 'connection.php';
include 'img_upload.php'; // Include the image handling functions

function getAllCategories($pdo) {
    return $pdo->query("SELECT category_id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Initial setup
    $action = isset($_POST['insert_product']) ? 'insert' : (isset($_POST['update_product']) ? 'update' : '');
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $character = $_POST['character'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $imageURL = '';  // Initialize as empty

    if ($action === 'update') {
        // First, fetch the current image URL to be able to retain it if no new image is provided
        $currentImageStmt = $pdo->prepare("SELECT image_url FROM Anime_Figures WHERE figure_id = ?");
        $currentImageStmt->execute([$id]);
        $currentImageURL = $currentImageStmt->fetchColumn();

        // Check if a new image URL is provided
        if (!empty($_POST['imageUrlUpdate'])) {
            $imageURL = $_POST['imageUrlUpdate'];
        } elseif (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
            // Process new image upload
            $uploadResult = handle_file_upload($name);  // Updated to use product name
            if ($uploadResult !== null) {
                $imageURL = 'figures_img/' . $uploadResult;
            }
        } elseif (isset($_POST['delete_image'])) {
            // Delete the image if the checkbox is checked
            $imageURL = null;
            // Also delete the actual file
            if ($currentImageURL) {
                $filePath = __DIR__ . '/' . $currentImageURL;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        } else {
            // If no new image is uploaded and delete is not checked, retain the current image
            $imageURL = $currentImageURL;
        }
        
        // Update the product details in the database
        $stmt = $pdo->prepare("UPDATE Anime_Figures SET name = ?, category_id = ?, `character` = ?, price = ?, description = ?, image_url = ? WHERE figure_id = ?");
        $stmt->execute([$name, $category_id, $character, $price, $description, $imageURL, $id]);
    }
    
    // Insert operation remains unchanged
    if ($action === 'insert') {
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handle_file_upload($name);
            if ($uploadResult !== null) {
                $imageURL = 'figures_img/' . $uploadResult;
            }
        } elseif (!empty($_POST['imageUrl'])) {
            $imageURL = $_POST['imageUrl'];
        }
        $stmt = $pdo->prepare("INSERT INTO Anime_Figures (name, category_id, `character`, price, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category_id, $character, $price, $description, $imageURL]);
    }

    header("Location: admin_panel.php"); // Redirect after processing
    exit;
}
