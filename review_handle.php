<?php
require 'connection.php';


// Ensure an ID is provided
if (!isset($_POST['review_id'])) {
    echo "Review ID is required.";
    exit;
}

// Ensure only admin users can delete reviews
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo "You are not authorized to delete reviews.";
    exit;
}

$reviewId = $_POST['review_id'];

// Delete the review from the database
$deleteReviewStmt = $pdo->prepare("DELETE FROM reviews WHERE review_id = ?");
$deleteReviewStmt->execute([$reviewId]);

// Redirect back to the product view page
if (isset($_POST['figure_id'])) {
    header("Location: product_view.php?id={$_POST['figure_id']}");
} else {
    echo "Product ID is required.";
    exit;
}

if (isset($_POST['hide_review']) || isset($_POST['unhide_review'])) {
    $reviewId = $_POST['review_id'];
    $status = isset($_POST['hide_review']) ? 'hidden' : 'visible';
    
    $updateStatusStmt = $pdo->prepare("UPDATE reviews SET status = ? WHERE review_id = ?");
    $updateStatusStmt->execute([$status, $reviewId]);
    
    // Redirect back to the product view page
    header("Location: product_view.php?id={$_POST['figure_id']}");
    exit;
}