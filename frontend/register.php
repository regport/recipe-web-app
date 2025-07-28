<?php
require 'includes/header.php';
$errors = [];
if($_SERVER['REQUEST_METHOD']==='POST') {
  $name  = trim($_POST['name']);
  $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
  $pwd   = $_POST['password'];
  if(!$name)  $errors[]='Name is required.';
  if(!$email) $errors[]='Valid email is required.';
  if(strlen($pwd)<6) $errors[]='Password ≥6 chars.';
  // Check duplicate
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
  $stmt->execute([$email]);
  if($stmt->fetch()) $errors[]='Email already registered.';
  if(!$errors) {
    $hash = hash('sha256',$pwd);
    $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash) VALUES (?,?,?)");
    $stmt->execute([$name,$email,$hash]);
    header('Location: login.php'); exit;
  }
}
?>
<h1>Register</h1>
<?php foreach($errors as $e): ?>
  <p class="error"><?= htmlentities($e) ?></p>
<?php endforeach; ?>
<form method="post">
  <label>Name <input name="name" required></label>
  <label>Email <input name="email" type="email" required></label>
  <label>Password <input name="password" type="password" required></label>
  <button type="submit">Register</button>
</form>
<?php require 'includes/footer.php'; ?>
