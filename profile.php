<?php
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$uid = $_SESSION['user_id'];

// Fetch user details
$stmtUser = $pdo->prepare("SELECT name, email, created_at FROM users WHERE id=?");
$stmtUser->execute([$uid]);
$user = $stmtUser->fetch();

// Fetch favourites
$stmtFavs = $pdo->prepare("
    SELECT r.id, r.name, r.image
    FROM recipes r
    JOIN favourites f ON f.recipe_id = r.id
    WHERE f.user_id = ?
");
$stmtFavs->execute([$uid]);
$favs = $stmtFavs->fetchAll();
?>

<main class="profile-container">
    <div class="user-header">
        <img src="img/profile-icon.png" alt="User Profile Picture" class="profile-pic">
        <h1 class="username"><?= htmlentities($user['name']) ?></h1>
    </div>

    <section class="profile-info">
        <div class="profile-header-row">
            <h2>Profile</h2>
        </div>
        <p class="join-date">Join date: <?= date('M d, Y', strtotime($user['created_at'])) ?></p>
        <hr />
        <table>
            <tr>
                <th>Name</th>
                <td><?= htmlentities($user['name']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlentities($user['email']) ?></td>
            </tr>
            <tr>
                <th>Account Created</th>
                <td><?= $user['created_at'] ?></td>
            </tr>
        </table>
    </section>


    <section class="saved-recipes">
        <br>
        <a href="search.php" class="button"><h2>Search for Recipes</h2></a>
        <br>
        <h2>Your Favourites</h2>
        <div class="recipe-grid">
            <?php if (count($favs) === 0): ?>
                <p>You haven’t saved any recipes yet.</p>
            <?php else: ?>
                <?php foreach ($favs as $recipe): ?>
                    <?php
                    $imgPath = !empty($recipe['image']) && file_exists("" . $recipe['image'])
                        ? "" . $recipe['image']
                        : "img/placeholder.jpg";
                    ?>
                    <div class="recipe-card">
                        <img src="<?= $imgPath ?>" alt="Recipe Image">
                        <h3><?= htmlentities($recipe['name']) ?></h3>
                        <a href="recipe-detail.php?id=<?= $recipe['id'] ?>" class="btn">View Recipe</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php require 'includes/footer.php'; ?>