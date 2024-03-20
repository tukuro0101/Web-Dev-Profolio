<?php // Include your database connection file
require 'admin_safety.php';
// Redirect user to login page if they're not logged in or not an admin


// Your product management code will go here

// Example: Inserting a new product
if (isset($_POST['insert_product'])) {
    // Gather data from the form
    $name = $_POST['name'];
    $category = $_POST['category'];
    $character = $_POST['character'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $imageURL = $_POST['image_url'];
    
    // Insert data into the database
    $stmt = $pdo->prepare("INSERT INTO Anime_Figure (`Name`, `Category`, `Character`, `Price`, `Description`, `Image_URL`) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $category, $character, $price, $description, $imageURL]);
    // Add error handling and success message
}

// Similar sections for 'update' and 'delete' actions will be needed

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'style.php'; ?>
    <title>Admin Panel</title>
</head>
<body>
    <div class="page_container">
        <header><?php include 'nav.php'; ?></header>

        <main>
            <h1>Admin Panel</h1>
        <section class="product-management">
            <h2>Insert New Product</h2>
            <form action="admin_panel.php" method="post">
                <input type="text" name="name" placeholder="Product Name" required>
                <input type="text" name="category" placeholder="Category" required>
                <input type="text" name="character" placeholder="Character" required>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="text" name="image_url" placeholder="Image URL" required>
                <button type="submit" name="insert_product">Insert Product</button>
            </form>
        </section>


    </main>

    <footer><?php include 'contact.php'; ?></footer>
</div>
    
</body>
</html>
