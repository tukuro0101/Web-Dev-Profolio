<?php
include 'connection.php';

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
    $imageURL = '';

    // Check for image URL input or file upload
    if (!empty($_POST['imageUrl']) || !empty($_POST['imageUrlUpdate'])) {
        // Use the provided image URL from form input
        $imageURL = !empty($_POST['imageUrl']) ? $_POST['imageUrl'] : $_POST['imageUrlUpdate'];
    } elseif (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        // Process file upload and set $imageURL
        $uploadResult = handle_file_upload($id); // Assume this function returns the path or null
        if ($uploadResult !== null) {
            $imageURL = 'figures_img/' . $uploadResult; // Construct the path for database storage
        }
    }

    

    // Determine if it's an update action and the "remove image" checkbox has been selected
    if ($action === 'update' && isset($_POST['delete_image'])) {
        $stmt = $pdo->prepare("SELECT image_url FROM Anime_Figures WHERE figure_id = ?");
        $stmt->execute([$id]);
        $currentImageURL = $stmt->fetchColumn();
        
        if ($currentImageURL) {
            $filename = basename($currentImageURL);
            $originalImagePath = __DIR__ . '/figures_img/' . $filename;
            $resizedImagePath = __DIR__ . '/figures_img/resized_' . $filename;

            if (file_exists($originalImagePath)) {
                unlink($originalImagePath);
            }
            if (file_exists($resizedImagePath)) {
                unlink($resizedImagePath);
            }
            
            $stmt = $pdo->prepare("UPDATE Anime_Figures SET image_url = NULL WHERE figure_id = ?");
            $stmt->execute([$id]);
        }
    }

    if ($action === 'insert') {
        $stmt = $pdo->prepare("INSERT INTO Anime_Figures (name, category_id, `character`, price, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$name, $category_id, $character, $price, $description, $imageURL]);
    } elseif ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE Anime_Figures SET name = ?, category_id = ?, `character` = ?, price = ?, description = ?, image_url = ? WHERE figure_id = ?");
        $stmt->execute([$name, $category_id, $character, $price, $description, $imageURL, $id]);
    }

    // Process the image file if uploaded
        $imageFileName = handle_file_upload($id);
        if ($imageFileName) {
            $imageURL = 'figures_img/' . $imageFileName; // Assuming you want to store the relative path in the database
            $stmt = $pdo->prepare("UPDATE Anime_Figures SET image_url = ? WHERE figure_id = ?");
            $stmt->execute([$imageURL, $id]);
        }

    if (isset($_POST['delete_product'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT image_url FROM Anime_Figures WHERE figure_id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product) {
            // Delete image files
            $imagePaths = [
                __DIR__ . '/figures_img/' . basename($product['image_url']),
                __DIR__ . '/figures_img/resized_' . basename($product['image_url']),
            ];

            foreach ($imagePaths as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Delete the product record
            $deleteStmt = $pdo->prepare("DELETE FROM Anime_Figures WHERE figure_id = ?");
            if ($deleteStmt->execute([$id])) {
                // Success
                header("Location: admin_panel.php?message=Product+Deleted+Successfully");
                exit;
            }}}

    header("Location: admin_panel.php"); // Redirect to the admin panel after processing
    exit;
}



function resize_image($file, $target_file) {
    list($width, $height, $type) = getimagesize($file);
    $new_width = $new_height = 300;  //300px x 300px

    if ($width > $height) {
        $new_height = ($new_width / $width) * $height;
    } else {
        $new_width = ($new_height / $height) * $width;
    }

    $thumb = imagecreatetruecolor($new_width, $new_height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($file);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($file);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($file);
            break;
        default:
            return false;
    }

    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $target_file);
            break;
            case IMAGETYPE_PNG:
                imagepng($thumb, $target_file);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumb, $target_file);
                break;
        }
    
        imagedestroy($thumb);
        imagedestroy($source);
    
        return true;
    }
    function handle_file_upload($productId) {
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] == UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . '/figures_img/';
            $fileType = strtolower(pathinfo($_FILES['imageFile']['name'], PATHINFO_EXTENSION));
            $fileName = $productId . '.' . $fileType; // Concatenate product ID with file extension
            $targetFilePath = $targetDir . $fileName;
    
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileType, $allowedTypes)) {
                echo "Invalid file type.";
                return null;
            }
    
            // Check if the temporary file exists
            if (!file_exists($_FILES['imageFile']['tmp_name'])) {
                echo "Error: Temporary file does not exist.";
                return null;
            }
    
            // Move the uploaded file to the target directory with the generated file name
            if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $targetFilePath)) {
                return $fileName; // Return the generated file name
            } else {
                echo "Error moving file.";
                return null;
            }
        }
    
        return null; // No file was uploaded
    }
    
    
    
    
    
    // img test
    function file_is_an_image($temporary_path, $new_path) {
        $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
        $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
        $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
        $actual_mime_type = getimagesize($temporary_path)['mime'];
    
        $file_extension_is_valid = in_array(strtolower($actual_file_extension), $allowed_file_extensions);
        $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);
    
        return $file_extension_is_valid && $mime_type_is_valid;
    }
    
    ?>
    