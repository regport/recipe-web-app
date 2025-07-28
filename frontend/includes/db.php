<?php
// includes/db.php
$host = '127.0.0.1';
$db   = 'recipe_app';
$user = 'recipe-app';
$pass = 'AppPass2025!';
$opts = [ PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION ];
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
  $pdo = new PDO($dsn, $user, $pass, $opts);
} catch(PDOException $e) {
  die("DB Error: " . $e->getMessage());
}
session_start();
?>
