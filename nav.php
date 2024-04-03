<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css"> 
    <link rel="stylesheet" href="reset.css">   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 
</head>
<body>
<?php
// Error handling
if (isset($results['error'])) {
    echo '<div class="error-message">' . $results['error'] . '</div>';
}
?>
<div class="navbar">
    <div class="nav_Title_container">
       <h1> Kuro Aniz CMS</h1>
    </div>
    <div class="nav_search_container">
        <form action="main.php" method="GET" id="searchForm">
        <input type="text" placeholder="Search.." name="searchQuery" id="searchInput" autocomplete="off" required value="<?php echo isset($_GET['searchQuery']) ? htmlspecialchars($_GET['searchQuery']) : ''; ?>">
            <div id="autocompleteList" class="autocomplete-items"></div>
            <button type="submit">Search</button>
        </form>
    </div>
    <a href="index.php">Home</a>
    <a href="main.php">Products</a>
    <a href="aboutme.php">About me</a>
    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
        <a href="admin_panel.php">Admin Panel</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['username'])): ?>
        <a href="authenticate.php?logout">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const autocompleteList = document.getElementById('autocompleteList');

    searchForm.addEventListener('submit', function(event) {
    // Prevent the default form submission
    event.preventDefault();

    const searchTerm = searchInput.value.trim();

    if (!searchTerm) {
        return;
    }

    // Encode the search term to ensure spaces and special characters are handled correctly
    const encodedSearchTerm = encodeURIComponent(searchTerm);

    // Redirect to main.php with the search query parameter
    window.location.href = `main.php?searchQuery=${encodedSearchTerm}`;
    });
});
</script>
</body>
</html>





<style>


.navbar {
    overflow: hidden;
    background-color: #333;
    font-family: Arial, sans-serif;
    position: sticky;
    padding: 10px;
}

.navbar a {
    float: right;
    font-size: 16px;
    color: white;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
}

.navbar a:hover {
    background-color: #ddd;
    color: black;
}

/* Style the search box */
.nav_search_container {
    float: left;
    padding : 0 15px;
}

.nav_search_container input[type=text] {
    padding: 6px;
    margin-top: 8px;
    font-size: 17px;
    border: none;
}

.nav_search_containernav_search_container button {
    float: right;
    padding: 6px 10px;
    margin-top: 8px;
    margin-right: 16px;
    background: #ddd;
    font-size: 17px;
    border: none;
    cursor: pointer;
}

.nav_search_container button:hover {
    background: #ccc;
} 
.autocomplete-items {
position: absolute;
border: 1px solid #d4d4d4;
border-bottom: none;
border-top: none;
z-index: 99;
/*position the autocomplete items to be the same width as the container:*/
top: 100%;
left: 0;
right: 0;
}

.autocomplete-items div {
padding: 10px;
cursor: pointer;
background-color: #fff; 
border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
background-color: #e9e9e9; 
}
    </style>