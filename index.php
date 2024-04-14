<?php
require 'connection.php';
session_start();
// Fetch the 5 most recent products
$stmt = $pdo->query("SELECT * FROM anime_figures ORDER BY date_added DESC LIMIT 8");
$recent_products = $stmt->fetchAll();
?>

<!DOCTYPE html>     
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Products</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

</head>
<body>
<div class="container">
        <header><?php include 'nav.php'; ?></header>
        <main>
        <section class="mt-4 mb-4">
            <h2>Recent Products</h2>
            <div class="product-slide-container mt-3">
                <div class="d-flex overflow-hidden position-relative product_container">
                    <?php foreach ($recent_products as $product): ?>
                        <div class="card me-3" style="width: 18rem;">
                            <img class="card-img-top" src="<?= htmlspecialchars($product['image_url']) ?>" alt="Product Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">
                                <a href="product_view.php?id=<?= $product['figure_id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h5>
                                <p class="card-text">Description: <?= htmlspecialchars(substr($product['description'], 0, 200)) . (strlen($product['description']) > 200 ? "..." : "") ?></p>
                                <p class="card-text"><strong>Price:</strong> $<?= htmlspecialchars(number_format($product['price'], 2)) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
        <footer><?php include 'contact.php'; ?></footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Slide animation for recent products
            var slideIndex = 0;
            var slideTimer;

            function showSlides() {
                var i;
                var slides = $('.card');
                for (i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";  
                }
                slideIndex++;
                if (slideIndex > slides.length) {slideIndex = 1}    
                slides[slideIndex-1].style.display = "block";  
                slideTimer = setTimeout(showSlides, 3000); // Change slide every 3 seconds
            }

            showSlides();

            $('.product-slide-container').on('mouseenter', function() {
                clearTimeout(slideTimer);
            }).on('mouseleave', function() {
                showSlides();
            });
        });
    </script>
    <style>
        /* Add your CSS styles for product display here */
        .product-slide-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 800px; /* Adjust height as needed */
        }
        .product_container{
            display: flex;
            justify-content: center;
        }

        .product-slide-wrapper {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .product-card {
            flex: 0 0 auto;
            width: 300px;
            margin-right: 20px; 
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            box-sizing: border-box;
        }
    </style>
</body>
</html>
