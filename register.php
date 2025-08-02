<?php
require 'includes/header.php';

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $pwd   = $_POST['password'];
    $confirmPwd = $_POST['confirmPassword'];

    if (!$name) $errors[] = 'Name is required.';
    if (!$email) $errors[] = 'Valid email is required.';
    if (strlen($pwd) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($pwd !== $confirmPwd) $errors[] = 'Passwords do not match.';

    // Check for existing email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = 'Email already registered.';

    if (!$errors) {
        $hash = hash('sha256', $pwd);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hash]);
        header('Location: login.php');
        exit;
    }
}
?>

<main class="form-page">
    <section class="form-container">
        <h1>Create an Account</h1>

        <?php if (!empty($errors)): ?>
            <div id="error-message" class="error-message">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlentities($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div id="error-message" class="error-message"></div>
        <?php endif; ?>

        <form id="registerForm" action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="name" value="<?= htmlentities($name) ?>" required />

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlentities($email) ?>" required />

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required />

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required />

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </section>
</main>

<?php require 'includes/footer.php'; ?>

<script>
document.getElementById('registerForm').addEventListener('submit', function (e) {
    const pw = document.getElementById('password').value;
    const cpw = document.getElementById('confirmPassword').value;
    const error = document.getElementById('error-message');

    if (pw !== cpw) {
        e.preventDefault();
        error.innerHTML = "<p>Passwords do not match.</p>";
    }
});
</script>
