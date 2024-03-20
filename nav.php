<?php session_start();?>
</head>
<body>

<div class="navbar">
    <div class="nav_search_container">
        <form action="/search.php">
            <input type="text" placeholder="Search.." name="search">
            <button type="submit">Submit</button>
        </form>
    </div>
    <div class="nav_Title_container">
       <h1> Kuro Aniz CMS</h1>
    </div>
    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
        <a href="admin_panel.php">Admin Panel</a>
    <?php endif; ?>
    <a href="index.php">Home</a>
    <a href="main.php">Products</a>
    <a href="aboutme.php">About me</a>
    <!-- Check if user is logged in and display either Login or Logout -->
    <?php if (isset($_SESSION['username'])): ?>
        <a href="authenticate.php?logout">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</div>

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
    </style>