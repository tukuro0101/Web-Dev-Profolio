<?php include 'main_handle.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Listing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header><?php include 'nav.php'; ?></header>
        
        <!-- Main Content -->
        <main style="display: flex;">
            <!-- Sidebar -->
            <aside class="category-sidebar">
                <h2>Categories</h2>
                <form id="categoryForm" action="" method="get">
                    <select name="category" id="categorySelect" onchange="this.form.submit();">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['category_id']) ?>" <?= ($categoryFilter == $category['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Retain other parameters -->
                    <?php if (isset($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['searchQuery'])): ?>
                        <input type="hidden" name="searchQuery" value="<?= htmlspecialchars($_GET['searchQuery']) ?>">
                    <?php endif; ?>
                </form>
            </aside>

            <!-- Sort Form -->
            <form id="sortForm" action="" method="get">
                <select name="sort" id="sortSelect" onchange="this.form.submit();">
                    <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Price Low to High</option>
                    <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Price High to Low</option>
                    <option value="date_added_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_added_desc') ? 'selected' : '' ?>>Added Recently</option>
                    <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>Alphabetical</option>
                    <option value="name_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : '' ?>>Reversed Alphabetical</option>
                </select>
                <!-- Retain the search query -->
                <?php if (isset($_GET['searchQuery'])): ?>
                    <input type="hidden" name="searchQuery" value="<?= htmlspecialchars($_GET['searchQuery']) ?>">
                <?php endif; ?>
                <!-- Retain the category filter -->
                <?php if (isset($_GET['category'])): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($_GET['category']) ?>">
                <?php endif; ?>
            </form>
<!-- Product Count Adjustment Form -->
<?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
    <form id="productCountForm" action="" method="get">
        <label for="productCount">Product Count:</label>
        <input type="number" id="productCount" name="productCount" min="1" value="<?= $productsPerPage ?>">
        <button type="submit">Update</button>
        <!-- Hidden input to store the product count for pagination -->
        <input type="hidden" name="prevProductCount" value="<?= $productsPerPage ?>">
    </form>
<?php endif; ?>
<div class="search-info">
    <?php if (isset($_GET['searchQuery'])): ?>
        <?php $searchTerm = htmlspecialchars($_GET['searchQuery']); ?>
        <p>1 - <?= min($totalProducts, $productsPerPage) ?> of <?= $totalProducts ?> results for '<?php echo $searchTerm ?>'</p>
    <?php endif; ?>
</div>

            <!-- Product Listing Section -->
            <section class="product-listing">
                <?php foreach ($products as $product): ?>
                    <div class="product">
                        <h2><b><?= htmlspecialchars($product['name']) ?></b></h2>
                        <h1>$<?= htmlspecialchars($product['price']) ?></h1>
                        <h5>Character : <?= htmlspecialchars($product['character']) ?></h5>
                        <h5>Added : <?= htmlspecialchars($product['date_added']) ?></h5>
                        <!-- Make the product image clickable -->
                        <?php if (!empty($product['image_url'])): ?>
                <a href="product_view.php?id=<?= $product['figure_id'] ?>">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Image of <?= htmlspecialchars($product['name']) ?>" width="300" height="300">
                </a>
            <?php endif; ?>
                        <h1><b><a href="product_view.php?id=<?= $product['figure_id'] ?>"><?= htmlspecialchars($product['name']) ?></a></b></h1>
                        <!-- Edit Link for admins -->
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
    <a href="admin_panel.php?edit_product_id=<?= $product['figure_id'] ?>">Edit</a>
<?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>

<!-- Pagination Links -->
<div class="pagination">
    <?php if ($totalProducts > $productsPerPage): ?>
        <?php if ($totalPages > 1): ?>
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&productCount=<?= $productsPerPage ?>&<?php if ($categoryFilter) echo 'category=' . urlencode($categoryFilter) ?>&<?php if ($sortOption) echo 'sort=' . $sortOption ?>&<?php if ($searchQuery) echo 'searchQuery=' . urlencode($searchQuery) ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&productCount=<?= $productsPerPage ?>&<?php if ($categoryFilter) echo 'category=' . urlencode($categoryFilter) ?>&<?php if ($sortOption) echo 'sort=' . $sortOption ?>&<?php if ($searchQuery) echo 'searchQuery=' . urlencode($searchQuery) ?>" <?= ($page == $i) ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&productCount=<?= $productsPerPage ?>&<?php if ($categoryFilter) echo 'category=' . urlencode($categoryFilter) ?>&<?php if ($sortOption) echo 'sort=' . $sortOption ?>&<?php if ($searchQuery) echo 'searchQuery=' . urlencode($searchQuery) ?>">Next</a>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>


        </main>

        <!-- Footer -->
        <footer><?php include 'contact.php';?></footer>
    </div>

    <!-- JavaScript -->
    <script>
        document.getElementById('sortSelect').addEventListener('change', function() {
            this.form.submit();
        });

        document.getElementById('categorySelect').addEventListener('change', function() {
            document.getElementById('categoryForm').submit();
        });

        // Function to load content without refreshing the page
function loadContent(url) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onload = function() {
        if (this.status === 200) {
            // Update the product listing section
            const response = document.createElement('div');
            response.innerHTML = this.responseText;

            // Replace the old content with the new one
            const oldSection = document.querySelector('.product-listing');
            const newSection = response.querySelector('.product-listing');
            oldSection.parentNode.replaceChild(newSection, oldSection);

            // Update pagination links
            const oldPagination = document.querySelector('.pagination');
            const newPagination = response.querySelector('.pagination');
            oldPagination.parentNode.replaceChild(newPagination, oldPagination);

            // Re-bind events to new pagination links
            bindPaginationEvents();
        } else {
            console.error('Failed to load the page content');
        }
    };
    xhr.send();
}

// Bind events to pagination links
function bindPaginationEvents() {
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            loadContent(this.href);
        });
    });
}

// Initial bind
bindPaginationEvents();

// Bind event to sort and category change without page refresh
document.querySelectorAll('#sortForm select, #categoryForm select').forEach(select => {
    select.addEventListener('change', function() {
        const form = this.closest('form');
        const url = form.action + '?' + new URLSearchParams(new FormData(form)).toString();
        loadContent(url);
    });
});

    </script>
</body>
</html>
