<?php
if(!isset($pdo)) require __DIR__.'/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= $pageTitle ?? 'Recipe App' ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
  <nav>
    <a href="index.php">Home</a>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="account.php">My Account</a>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </nav>
</header>
<main>
