<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css"> 
    <link rel="stylesheet" href="reset.css">   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="nav_container">
         <div class="Title_container">
       <h1> Kuro Aniz CMS</h1>
    </div>
<div class="navbar">
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
        <a href="login.php?logout">Logout</a>

    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</div>

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


.nav_container {
    overflow: hidden;
    background-image: linear-gradient(to right bottom, #3f3f3f, #343434, #292929, #1f1f1f, #151515, #151515, #161616, #161616, #212121, #2d2d2d, #393939, #464646);
    font-family: Arial, sans-serif;
    position: sticky;
    padding: 10px;
    margin-bottom: 25px;
    border-radius: 30px;
}
.Title_container{ font-size: xxx-large;
    text-align: center;
    color: whitesmoke;}

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
a{text-decoration: none !important}
    </style>