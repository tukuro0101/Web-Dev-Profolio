<?php
require 'connection.php';

// Ensure an ID is provided
if (!isset($_GET['id'])) {
    echo "Product ID is required.";
    exit;
}

// Fetch the product details
$stmt = $pdo->prepare("SELECT * FROM anime_figures WHERE figure_id = ?");
$stmt->execute([$_GET['id']]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found.";
    exit;
}

// Fetch the category name for the product
$categoryStmt = $pdo->prepare("SELECT name FROM categories WHERE category_id = ?");
$categoryStmt->execute([$product['category_id']]);
$categoryName = $categoryStmt->fetchColumn();

// Fetch the reviews for the product in reverse chronological order
$reviewsStmt = $pdo->prepare("SELECT r.rating, r.comment, r.review_id, r.status, r.reviewer_type, r.date_commented, 
                                    r.guest_name, u.username
                              FROM reviews r 
                              LEFT JOIN users u ON r.user_id = u.user_id 
                              WHERE r.figure_id = ?
                              ORDER BY r.date_commented DESC");
$reviewsStmt->execute([$_GET['id']]);
$reviews = $reviewsStmt->fetchAll();


// Review Submission Logic
if (isset($_POST['submit_review'])) {
    // Validate comment data
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    if (isset($_SESSION['user_id'])) {
        // User is logged in, use their user_id
        $userId = $_SESSION['user_id'];
        $reviewerType = 'registered';
    } else {
        // User is not logged in, determine reviewer type and name
        if (!empty($_POST['guest_name'])) {
            // If guest name provided, use it as the reviewer name
            $guestName = $_POST['guest_name'];
            $reviewerType = 'guest';
        } else {
            // If guest name not provided, mark as anonymous
            $guestName = 'Anonymous';
            $reviewerType = 'anonymous';
        }

        // Insert the review with guest name
        $insertReviewStmt = $pdo->prepare("INSERT INTO reviews (figure_id, rating, comment, date_commented, reviewer_type, guest_name) VALUES (?, ?, ?, NOW(), ?, ?)");
        $insertReviewStmt->execute([$_GET['id'], $rating, $comment, $reviewerType, $guestName]);

        // Redirect to avoid resubmission on refresh
        header("Location: product_view.php?id={$_GET['id']}");
        exit;
    }

    // Insert the review for registered users
    $insertReviewStmt = $pdo->prepare("INSERT INTO reviews (figure_id, user_id, rating, comment, date_commented, reviewer_type) VALUES (?, ?, ?, ?, NOW(), ?)");
    $insertReviewStmt->execute([$_GET['id'], $userId, $rating, $comment, $reviewerType]);

    // Redirect to avoid resubmission on refresh
    header("Location: product_view.php?id={$_GET['id']}");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?></title>
</head>
<body>
    <div class="page_container">
        <header><?php include 'nav.php'; ?></header>
        
        <main>
            <section class="product-details">
                <h1><?= htmlspecialchars($product['name']) ?></h1>
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Product Image" width="300" height="300">
                <p>Category: <?= htmlspecialchars($categoryName) ?></p>
                <p>Character: <?= htmlspecialchars($product['character']) ?></p>
                <p>Description: <?= htmlspecialchars($product['description']) ?></p>
                <p>Price: $<?= htmlspecialchars(number_format($product['price'], 2)) ?></p>
            </section>
            <section class="reviews">
                <h2>Reviews</h2>
                <div class="reviews-container">
                <?php foreach ($reviews as $review): ?>
                    <?php if ($review['status'] === 'visible' || (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin')): ?>
        <div class="review" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
            <?php
                $reviewerName = 'Anonymous'; // Default to anonymous
                if ($review['reviewer_type'] === 'registered' && !empty($review['username'])) {
                    // If registered and username is not empty, display username
                    $reviewerName = $review['username'];
                } elseif ($review['reviewer_type'] === 'guest') {
                    // If guest, display guest_name or 'Anonymous' if guest_name is empty
                    $reviewerName = !empty($review['guest_name']) ? $review['guest_name'] : 'Anonymous';
                }
            ?>
            <strong><?= htmlspecialchars($reviewerName) ?></strong>
            <p>Rating: <?= htmlspecialchars($review['rating']) ?>/5</p>
            <p><?= htmlspecialchars($review['comment']) ?></p>
            <p><?= htmlspecialchars(date('F d,Y',strtotime($review['date_commented']))) ?></p>
             <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                <form action="review_handle.php" method="post">
                                <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
                                <input type="hidden" name="figure_id" value="<?= $_GET['id'] ?>"> <!-- Include figure_id for redirection -->
                                <?php if ($review['status'] === 'visible'): ?>
                                    <button type="submit" name="hide_review">Hide</button>
                                <?php else: ?>
                                    <button type="submit" name="unhide_review">Unhide</button>
                                <?php endif; ?>
                                <button type="submit" name="delete_review">Delete</button>
                            </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
                </div>
            </section>
            <section class="comment-form">
                <h2>Leave a Review</h2>
                <form action="product_view.php?id=<?= $_GET['id'] ?>" method="post">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <label for="guest_name">Your Name:</label>
                        <input type="text" name="guest_name" id="guest_name">
                    <?php endif; ?>
                    <label for="rating">Rating:</label>
                    <select name="rating" id="rating" required>
                        <option value="">Choose a rating</option>
                        <option value="1">1 - Poor</option>
                        <option value="2">2 - Fair</option>
                        <option value="3">3 - Good</option>
                        <option value="4">4 - Very Good</option>
                        <option value="5">5 - Excellent</option>
                    </select>
                    <label for="comment">Your Comment:</label>
                    <textarea name="comment" id="comment" required></textarea>
                    <button type="submit" name="submit_review">Submit Review</button>
                </form>
            </section>
        </main>
        <footer><?php include 'contact.php'; ?></footer>
    </div>
    <script>
        document.getElementById('toggleReviews').addEventListener('click', function() {
            document.querySelectorAll('.review.hidden').forEach(function(review) {
                review.classList.remove('hidden');
            });
            this.style.display = 'none'; 
        });
    </script>

</body>
<style>
    .page_container {
        margin: 0 auto;
    }

    .reviews-container {
        max-height: 400px; 
        overflow-y: auto; 
        border: 1px solid #ccc; 
        padding: 10px; 
        margin-bottom: 20px;
    }

    .review {
        margin-bottom: 20px; 
    }

    #toggleReviews {
        display: none; 
    }
</style>
</html>
