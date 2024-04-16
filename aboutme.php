<?php
require 'connection.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
        <header> <?php include 'nav.php';?>  </header>
        <p>Info to be updated </p>
        <footer><?php include 'contact.php';?></footer>
       
    </div>
</body>
</html>