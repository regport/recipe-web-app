<?php
require 'includes/header.php';
if(!$_SESSION['user_id']) header('Location: login.php');
$uid = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name,email,joined FROM users WHERE id=?");
$stmt->execute([$uid]);
$user = $stmt->fetch();
?>
<h1>Your Profile</h1>
<table>
  <tr><th>Name</th><td><?= htmlentities($user['name']) ?></td></tr>
  <tr><th>Email</th><td><?= htmlentities($user['email']) ?></td></tr>
  <tr><th>Joined</th><td><?= $user['joined'] ?></td></tr>
</table>
<?php require 'includes/footer.php'; ?>
