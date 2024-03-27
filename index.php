<?php
require 'connection.php';

// Fetch the 5 most recent products
$stmt = $pdo->query("SELECT * FROM anime_figures ORDER BY date_added DESC LIMIT 5");
$recent_products = $stmt->fetchAll();
?>

<!DOCTYPE html>     
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Products</title>
</head>
<body>
    <div class="page_container">
        <header><?php include 'nav.php'; ?></header>
        
        <main>
            <section class="recent-products">
                <h2>Recent Products</h2>
                <div class="product-slide-container">
                    <div class="product-slide-wrapper">
                        <?php foreach ($recent_products as $product): ?>
                            <div class="product-card">
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Product Image" width="100%" height="200">
                                <p>Description: <?= htmlspecialchars($product['description']) ?></p>
                                <p>Price: $<?= htmlspecialchars(number_format($product['price'], 2)) ?></p>
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
                var slides = $('.product-card');
                for (i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";  
                }
                slideIndex++;
                if (slideIndex > slides.length) {slideIndex = 1}    
                slides[slideIndex-1].style.display = "block";  
                slideTimer = setTimeout(showSlides, 3000); // Change slide every 3 seconds
            }

            showSlides();

            $('.product-slide-wrapper').on('mouseenter', function() {
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
            height: 400px; /* Adjust height as needed */
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
