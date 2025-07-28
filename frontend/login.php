<?php
require 'includes/header.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST') {
  $email = $_POST['email'];
  $pwd   = $_POST['password'];
  $stmt = $pdo->prepare("SELECT id,password_hash FROM users WHERE email=?");
  $stmt->execute([$email]);
  $u = $stmt->fetch();
  if($u && hash('sha256',$pwd)===$u['password_hash']) {
    $_SESSION['user_id']=$u['id'];
    header('Location: account.php'); exit;
  }
  $err = 'Invalid credentials.';
}
?>
<h1>Login</h1>
<p class="error"><?= $err ?></p>
<form method="post">
  <label>Email <input name="email" type="email" required></label>
  <label>Password <input name="password" type="password" required></label>
  <button type="submit">Login</button>
</form>
<?php require 'includes/footer.php'; ?>
