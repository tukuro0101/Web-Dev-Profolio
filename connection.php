<?php
     define('DB_DSN','mysql:host=localhost;dbname=Kuro_Aniz_CMS;charset=utf8');
      define('DB_USER','admin123');
     define('DB_PASS','');


     $host = 'localhost';
     $db   = 'Kuro_Aniz_CMS';
     $user = 'admin123';
     $pass = '';
     $charset = 'utf8mb4';
     
     $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
     $options = [
         PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::ATTR_EMULATE_PREPARES   => false,
     ];
     
     try {
         $pdo = new PDO($dsn, $user, $pass, $options);
     } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
     }
     