<?php
if (!isset($pdo)) require __DIR__ . '/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= $pageTitle ?? 'Kitchen Cloud' ?></title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="container"> <!-- Outer Container for consistency -->
  <div class="header"> <!-- Header containing main top nav menu -->
    <a href="index.php"><img src="assets/img/Logo.jpg" alt="Website Logo" class="logo"></a>
    <button class="nav-toggle" aria-label="Toggle Navigation">☰</button>
    <nav aria-label="Main site navigation">
      <ul class="nav-menu">
        <li><a href="index.php">Home</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li><a href="profile.php">My Account</a></li>
          <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
        <?php endif; ?>
        <li><a href="search.php">Recipes</a></li>
      </ul>
    </nav>
  </div>
  <main>
