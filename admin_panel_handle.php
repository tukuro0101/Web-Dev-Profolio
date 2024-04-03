<?php
include 'connection.php';

// Function to fetch all categories
function getAllCategories($pdo) {
    return $pdo->query("SELECT category_id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
}

// Handle product actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['insert_product']) || isset($_POST['update_product'])) {
        $action = isset($_POST['insert_product']) ? 'insert' : 'update';

        // Retrieve form data
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $character = $_POST['character'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $imageURL = '';

        // Check if a new image is uploaded
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
            $targetDir = 'figures_img/';
            $targetFilePath = $targetDir . basename($_FILES["imageFile"]["name"]);
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $allowedTypes = array('jpg', 'png', 'jpeg', 'gif');

            // Check if the uploaded file is of an allowed type
            if (in_array($fileType, $allowedTypes)) {
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $targetFilePath)) {
                    $imageURL = $targetFilePath;
                } else {
                    echo "Error uploading file.";
                    exit;
                }
            } else {
                echo "Invalid file type.";
                exit;
            }
        } elseif (isset($_POST['imageUrl']) && !empty($_POST['imageUrl'])) {
            // Check if an image URL is provided for new product insertion
            $imageURL = $_POST['imageUrl'];
        } elseif (isset($_POST['imageUrlUpdate']) && !empty($_POST['imageUrlUpdate'])) {
             // Check if an image URL is provided for product update
             $imageURL = $_POST['imageUrlUpdate'];
            } else {
                // If no new image is uploaded or image URL is provided, retain the old image URL
                // Retrieve old image URL
                $stmt = $pdo->prepare("SELECT image_url FROM Anime_Figures WHERE figure_id = ?");
                $stmt->execute([$id]);
                $oldImage = $stmt->fetchColumn();
                
                $imageURL = $oldImage;
            }
    
            // Prepare SQL statement
            if ($action === 'insert') {
                $stmt = $pdo->prepare("INSERT INTO Anime_Figures (name, category_id, `character`, price, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
                // Execute SQL statement
                $result = $stmt->execute([$name, $category_id, $character, $price, $description, $imageURL]);
            } else {
                $stmt = $pdo->prepare("UPDATE Anime_Figures SET `name` = ?, `category_id` = ?, `character` = ?, `price` = ?, `description` = ?, `image_url` = ? WHERE `figure_id` = ?");
                // Execute SQL statement
                $result = $stmt->execute([$name, $category_id, $character, $price, $description, $imageURL, $id]);
            }
    
            // Handle result
            if ($result) {
                echo ($action === 'insert') ? "New product inserted successfully!" : "Product updated successfully!";
            } else {
                echo "Error processing product.";
            }
    
            // Redirect back to admin_panel.php
            header("Location: admin_panel.php");
            exit;
        }
    
        // Handle product deletion
        if (isset($_POST['delete_product'])) {
            $id = $_POST['id'];
            $deleteStmt = $pdo->prepare("DELETE FROM Anime_Figures WHERE figure_id = ?");
            echo $deleteStmt->execute([$id]) ? "Product deleted successfully!" : "Error deleting product.";
    
            // Redirect back to admin_panel.php
            header("Location: admin_panel.php");
            exit;
        }
    }

// File Upload
function file_upload_path($original_filename) {
    $baseDirectory = 'C:\\xampp\\htdocs\\wdtu\\GitHub\\Web-Dev-Profolio\\figures_img\\';
    return $baseDirectory . basename($original_filename);
}

// Image validation function
function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type = getimagesize($temporary_path)['mime'];
    $file_extension_is_valid = in_array(strtolower($actual_file_extension), $allowed_file_extensions);
    $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);
    return $file_extension_is_valid && $mime_type_is_valid;
}

// Check if image is uploaded
$image_upload_detected = isset($_FILES['imageFile']) && ($_FILES['imageFile']['error'] === 0);

// If image is uploaded
if ($image_upload_detected) {
    $image_filename = $_FILES['imageFile']['name'];
    $temporary_image_path = $_FILES['imageFile']['tmp_name'];
    $new_image_path = file_upload_path($image_filename);
    
    // Check if the uploaded file is an image
    if (file_is_an_image($temporary_image_path, $new_image_path)) {
        move_uploaded_file($temporary_image_path, $new_image_path);
    }
}
?>
