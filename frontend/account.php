<?php
require 'includes/header.php';
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = $_SESSION['user_id'];

// Fetch favourites
$stmt = $pdo->prepare("
  SELECT r.id,r.name 
  FROM recipes r
  JOIN favourites f ON f.recipe_id=r.id
  WHERE f.user_id=?
");
$stmt->execute([$uid]);
$favs = $stmt->fetchAll();

$stmtUser= $pdo->prepare("SELECT name, email, created_at from users where id=?");
$stmtUser->execute([$uid]);
$stmtUserDetail = $stmtUser->fetch();
?>
<h1>Welcome Back! <?= $stmtUserDetail['name'] ?></h1>
<p>Email: <?= $stmtUserDetail['email'] ?></p>
<p>Account created on: <?= $stmtUserDetail['created_at'] ?></p>
<br>
<a href="index.php" class="button">Search for Recipes</a>
<br>
<h2>Your Favourites</h2>
<ul>
<?php foreach($favs as $r): ?>
  <li><a href="recipe.php?id=<?= $r['id'] ?>"><?= htmlentities($r['name']) ?></a></li>
<?php endforeach; ?>
</ul>
<?php require 'includes/footer.php'; ?>
