<?php
$host = "localhost";
$dbname = "vividly";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}