<?php
require 'includes/header.php';
$err = '';
$u = [];

function check_login($pdo, $email, $pwd)
{

    $stmt = $pdo->prepare("SELECT id,password_hash FROM users WHERE email=?");
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    $result = false;
    if ($u && hash('sha256', $pwd) === $u['password_hash']) {
        $_SESSION['user_id'] = $u['id'];
        $result = true;
    }

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pwd = $_POST['password'] ?? '';

    if (check_login($pdo, $email, $pwd)) {
        header('Location: profile.php');
        exit;
    } else {
        $err = 'Invalid username or password.';
    }
}
?>
<main class="form-page">
    <section class="form-container">
        <h1>Login</h1>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>" />
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required />
            </div>

            <?php if ($err): ?>
                <p class="error-message"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>

            <button type="submit">Login</button>

            <p class="auth-link">
                Don’t have an account?
                <a href="register.php">Register here</a>
            </p>
        </form>
    </section>
</main>

<?php require 'includes/footer.php'; ?>