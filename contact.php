
<style>
        .footer {
            background-color: #333; /* Assuming this matches the nav color */
            color: white;
            padding: 20px 0;
            font-family: Arial, sans-serif;
        }
        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 1200px;
            margin: auto;
            padding: 0 20px;
        }
        .footer-section {
            flex-basis: 20%;
            padding: 10px;
            min-width: 150px;
        }
        .footer-section h3 {
            margin-bottom: 15px;
        }
        .footer-section ul {
            list-style: none;
            padding: 0;
        }
        .footer-section ul li a {
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            display: block;
        }
        .footer-section ul li a:hover {
            text-decoration: underline;
        }
        .newsletter {
            border: none;
            padding: 8px;
            width: 70%;
        }
        .submit-btn {
            background-color: #227722; /* Green color */
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
        }
        .social-icons {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .social-icons a {
            display: inline-block;
            margin: 0 5px;
        }
        .footer-bottom {
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid #444;
            padding-top: 10px;
        }
        .comment-form input[type="text"] {
            width: 70%; /* You can adjust the width as needed */
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ddd;
        }

        .comment-form input[type="submit"] {
            background-color: #227722; /* Green color */
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h3>Shop Matcha</h3>
            <ul>
                <li><a href="#">Just the Matcha</a></li>
                <li><a href="#">The Trial Kit</a></li>
                <li><a href="#">Wholesale & Bulk</a></li>
                <li><a href="#">Teaware</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Learn</h3>
            <ul>
                <li><a href="#">Matcha Recipes</a></li>
                <li><a href="#">Caffeine Content</a></li>
                <li><a href="#">Health Benefits</a></li>
            </ul>
        </div>

        <div class="footer-section">
        <h3>Leave a Comment</h3>
            <form action="" method="post" class="comment-form">
                <input type="text" name="comment" placeholder="Your Comment" required>
                <input type="submit" value="Submit">
            </form>
            <div class="social-icons">
                
                <div><a href="#"></a><i class="fa-brands fa-facebook"></i></div>
                <div><a href="#"></a><i class="fa-brands fa-instagram"></i></div>
                <div><a href="#"></a><i class="fa-brands fa-linkedin"></i></div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date("Y") ?> Tran Minh Tu's Website.I don't any photos that are displayed in the webiste.</p>
    </div>
</div>