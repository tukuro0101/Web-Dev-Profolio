<?php
require 'connection.php';

if (!isset($_POST['review_id'])) {
    echo "Review ID is required.";
    exit;
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo "You are not authorized to perform this action.";
    exit;
}

$reviewId = $_POST['review_id'];
$figureId = $_POST['figure_id'] ?? null; // Fallback to null if figure_id is not set

if (isset($_POST['hide_review']) || isset($_POST['unhide_review'])) {
    $status = isset($_POST['hide_review']) ? 'hidden' : 'visible';
    
    $updateStatusStmt = $pdo->prepare("UPDATE reviews SET status = ? WHERE review_id = ?");
    $updateStatusStmt->execute([$status, $reviewId]);
} elseif (isset($_POST['delete_review'])) {
    $deleteReviewStmt = $pdo->prepare("DELETE FROM reviews WHERE review_id = ?");
    $deleteReviewStmt->execute([$reviewId]);
} else {
    echo "Invalid action.";
    exit;
}

// Redirect back to the product view page, ensuring figure_id is included for correct redirection
header("Location: product_view.php?id=" . ($figureId ?: 'default_id'));
exit;
?>
