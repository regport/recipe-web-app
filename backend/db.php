<?php
// Database credentials
$host = 'localhost';
$dbname = 'recipe_app';
$user = '';
$pass = '';

try {
    // Establish PDO connection to MySQL database
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // Set error mode to throw exceptions for easier debugging
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Display error if connection fails
    die("Database connection failed: " . $e->getMessage());
}
?>
