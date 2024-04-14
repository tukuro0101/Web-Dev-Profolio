<?php
// Include the connection settings
include 'connection.php';
function resize_image($file, $target_file) {
    list($width, $height, $type) = getimagesize($file);
    $new_width = $new_height = 300; // 300px x 300px

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

function handle_file_upload($productName) {
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] == UPLOAD_ERR_OK) {
        $targetDir = __DIR__ . '/figures_img/';
        $fileType = strtolower(pathinfo($_FILES['imageFile']['name'], PATHINFO_EXTENSION));

        // Sanitize the product name to use in the file name
        $safeProductName = preg_replace("/[^a-zA-Z0-9]+/", "-", $productName);
        $resizedFileName = $safeProductName . '.' . $fileType;
        $resizedFilePath = $targetDir . $resizedFileName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileType, $allowedTypes)) {
            echo "Invalid file type.";
            return null;
        }

        // Resize the image
        if (resize_image($_FILES['imageFile']['tmp_name'], $resizedFilePath)) {
            return $resizedFileName; // Return the new filename
        } else {
            echo "Error resizing file.";
            return null;
        }
    }
    return null; // no file was uploaded
}


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
