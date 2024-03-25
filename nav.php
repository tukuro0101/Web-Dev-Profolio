</head>
<body>
<div class="navbar">
    <div class="nav_Title_container">
       <h1> Kuro Aniz CMS</h1>
    </div>
    <div class="nav_search_container">
    <form action="main.php" method="GET">
    <input type="text" placeholder="Search.." name="searchQuery" id="searchInput" autocomplete="off" required>
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
    <!-- Check if user is logged in and display either Login or Logout -->
    <?php if (isset($_SESSION['username'])): ?>
        <a href="authenticate.php?logout">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</div>
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const autocompleteList = document.getElementById('autocompleteList');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        if (!searchTerm) {
            autocompleteList.innerHTML = '';
            return;
        }

        // Make sure this URL matches your setup
        fetch(`product_search.php?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(products => {
                autocompleteList.innerHTML = '';
                products.forEach(product => {
                    const div = document.createElement('div');
                    div.textContent = product.name;
                    div.addEventListener('click', function() {
                        window.location.href = `product_view.php?id=${product.figure_id}`;
                    });
                    autocompleteList.appendChild(div);
                });
            })
            .catch(error => console.error('Error fetching products:', error));
    });
});

</script>
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