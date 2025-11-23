<?php
$host = 'localhost';
$dbname = 'web_recipe_app';
$username = 'root';
$password = '';

try {
    // PDO (PHP Data Objects) database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set error mode (to see errors during development)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    // If there is a connection error, show the message and stop the program
    die("Database connection failed: " . $e->getMessage());
}
?>