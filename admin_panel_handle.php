<?php
session_start();
include 'connection.php';
include 'img_upload.php';

function getAllCategories($pdo) {
    return $pdo->query("SELECT category_id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Determine the action based on which button was pressed
    if (isset($_POST['insert_product'])) {
        $action = 'insert';
    } elseif (isset($_POST['update_product'])) {
        $action = 'update';
    } elseif (isset($_POST['delete_product'])) {
        $action = 'delete';
    } else {
        $action = '';
    }

    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $character = $_POST['character'] ?? '';
    $price = $_POST['price'] ?? 0.00;
    $description = $_POST['description'] ?? '';
    $imageURL = '';  // Initialize as empty

    switch ($action) {
        case 'insert':
            // Handle image upload or URL input for new product
            if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = handle_file_upload($name);
                if ($uploadResult !== null) {
                    $imageURL = 'figures_img/' . $uploadResult;
                }
            } elseif (!empty($_POST['imageUrl'])) {
                $imageURL = $_POST['imageUrl'];
            }

            // Insert product into the database
            $stmt = $pdo->prepare("INSERT INTO Anime_Figures (name, category_id, `character`, price, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category_id, $character, $price, $description, $imageURL]);
            break;

        case 'update':
            // Fetch current image URL
            $currentImageStmt = $pdo->prepare("SELECT image_url FROM Anime_Figures WHERE figure_id = ?");
            $currentImageStmt->execute([$id]);
            $currentImageURL = $currentImageStmt->fetchColumn();

            // Handle new image or image deletion
            if (!empty($_POST['imageUrlUpdate'])) {
                $imageURL = $_POST['imageUrlUpdate'];
            } elseif (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = handle_file_upload($name);
                if ($uploadResult !== null) {
                    $imageURL = 'figures_img/' . $uploadResult;
                }
            } elseif (isset($_POST['delete_image'])) {
                $imageURL = null;
                if ($currentImageURL) {
                    $filePath = __DIR__ . '/' . $currentImageURL;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            } else {
                $imageURL = $currentImageURL;
            }

            // Update product in the database
            $stmt = $pdo->prepare("UPDATE Anime_Figures SET name = ?, category_id = ?, `character` = ?, price = ?, description = ?, image_url = ? WHERE figure_id = ?");
            $stmt->execute([$name, $category_id, $character, $price, $description, $imageURL, $id]);
            break;

        case 'delete':
            // Fetch current image URL for deletion
            $imageQuery = $pdo->prepare("SELECT image_url FROM Anime_Figures WHERE figure_id = ?");
            $imageQuery->execute([$id]);
            $imageURL = $imageQuery->fetchColumn();

            // Delete image file if it exists
            if ($imageURL) {
                $filePath = __DIR__ . '/' . $imageURL;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Delete the product from the database
            $stmt = $pdo->prepare("DELETE FROM Anime_Figures WHERE figure_id = ?");
            $stmt->execute([$id]);
            break;
    }

    header("Location: admin_panel.php"); // Redirect to avoid form resubmission
    exit;
}
?>
