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
<style>
    body{background: rgb(70,70,70);
background: linear-gradient(90deg, rgba(70,70,70,1) 0%, rgba(25,25,25,1) 20%, rgba(71,71,71,1) 40%, rgba(0,0,0,1) 60%, rgba(38,38,45,1) 80%, rgba(14,21,23,1) 100%);}
.container{background: whitesmoke;}
</style>
</html>