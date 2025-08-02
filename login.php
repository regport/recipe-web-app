<?php
require 'includes/header.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $pwd   = $_POST['password'];

  if (check_login($email, $pwd)) {
    header('Location: account.php');
    exit;
  } else {
    $err = 'Invalid credentials.';
  }
}

function check_login($email, $pwd)
{
  global $pdo;

  $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email=?");
  $stmt->execute([$email]);
  $u = $stmt->fetch();

  $result = false;
  if ($u && hash('sha256', $pwd) === $u['password_hash']) {
    $_SESSION['user_id'] = $u['id'];
    $result = true;
  }

  // 🔹 Logging: save login attempts in a file in your project
  $logMessage = date('Y-m-d H:i:s') . " - Login result for {$email}: " . ($result ? 'true' : 'false') . "\n";
  file_put_contents(__DIR__ . '/login_debug.log', $logMessage, FILE_APPEND);

  return $result;
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
