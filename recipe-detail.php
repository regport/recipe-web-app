<?php
require 'includes/header.php';

$uid = $_SESSION['user_id'];

$recipeId = intval($_GET['id'] ?? 0);

// Fetch recipe
$stmtRecipe = $pdo->prepare("SELECT * FROM recipes r WHERE r.id=?");
$stmtRecipe->execute([$recipeId]);
$recipe = $stmtRecipe->fetch();

// Fetch categories
$stmtCats = $pdo->prepare("SELECT c.name FROM recipe_categories rc JOIN categories c ON c.id = rc.category_id WHERE rc.recipe_id=?");
$stmtCats->execute([$recipeId]);
$catsResult = $stmtCats->fetchAll();
$cats = array_map(fn($c) => $c['name'], $catsResult);

// Fetch ingredients
$stmtIngredients = $pdo->prepare("SELECT ri.quantity as quantity, i.name as ingredient FROM recipe_ingredients ri JOIN ingredients i ON ri.ingredient_id = i.id WHERE ri.recipe_id=?");
$stmtIngredients->execute([$recipeId]);
$ingredients = $stmtIngredients->fetchAll();

// Fetch steps
$stmtSteps = $pdo->prepare("SELECT * FROM steps s WHERE s.recipe_id=?");
$stmtSteps->execute([$recipeId]);
$steps = $stmtSteps->fetchAll();

// Check if favourited
$stmtFavExists = $pdo->prepare("SELECT count(*) as favExists FROM favourites f WHERE f.user_id=? AND f.recipe_id=?");
$stmtFavExists->execute([$uid, $recipeId]);
$favExists = $stmtFavExists->fetchColumn();

// Fetch average ratings
$stmtAvgRatings = $pdo->prepare("SELECT avg(r.difficulty_score) as avg_difficulty_score, avg(r.aesthetics_score) as avg_aesthetics_score, avg(r.taste_score) as avg_taste_score, count(user_id) as no_of_users FROM ratings r WHERE r.recipe_id=?");
$stmtAvgRatings->execute([$recipeId]);
$avgRatings = $stmtAvgRatings->fetch();

// Fetch user's rating
$stmtUsersRating = $pdo->prepare("SELECT r.difficulty_score, r.aesthetics_score, r.taste_score FROM ratings r WHERE r.recipe_id=? AND r.user_id=?");
$stmtUsersRating->execute([$recipeId, $uid]);
$usersRating = $stmtUsersRating->fetch();

$ratingCategories = ['difficulty', 'aesthetics', 'taste'];
$selectedRatingScore = [
    'difficulty' => $usersRating['difficulty_score'] ?? 1,
    'aesthetics' => $usersRating['aesthetics_score'] ?? 1,
    'taste' => $usersRating['taste_score'] ?? 1
];

$error = null;

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['fav'])) {
            $favAction = $_POST['fav'];
            if ($favAction) {
                $stmt = $pdo->prepare("INSERT INTO favourites(user_id, recipe_id) VALUES (?, ?)");
                $stmt->execute([$uid, $recipeId]);
            } else {
                $stmt = $pdo->prepare("DELETE FROM favourites WHERE user_id=? AND recipe_id=?");
                $stmt->execute([$uid, $recipeId]);
            }
            header("Location: recipe-detail.php?id=$recipeId");
            exit;
        }

        if (isset($_POST['rate'])) {
            $selectedRatingScore['difficulty'] = $_POST['difficulty_score'];
            $selectedRatingScore['aesthetics'] = $_POST['aesthetics_score'];
            $selectedRatingScore['taste'] = $_POST['taste_score'];

            if (empty($usersRating)) {
                $stmt = $pdo->prepare("INSERT INTO ratings (user_id, recipe_id, difficulty_score, aesthetics_score, taste_score) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$uid, $recipeId, $selectedRatingScore['difficulty'], $selectedRatingScore['aesthetics'], $selectedRatingScore['taste']]);
            } else {
                $stmt = $pdo->prepare("UPDATE ratings SET difficulty_score=?, aesthetics_score=?, taste_score=? WHERE recipe_id=? AND user_id=?");
                $stmt->execute([$selectedRatingScore['difficulty'], $selectedRatingScore['aesthetics'], $selectedRatingScore['taste'], $recipeId, $uid]);
            }
            header("Location: recipe-detail.php?id=$recipeId");
            exit;
        }
    } catch (Exception $e) {
        $error = "Failed to update last change.";
    }
}
?>

<main class="recipe-detail">
    <section class="recipe-hero">
        <?php
        $imageFile = !empty($recipe['image']) && file_exists("" . $recipe['image'])
           ? "" . $recipe['image']
           : "img/placeholder.jpg";
        ?>
        <img src="<?= $imageFile ?>" alt="<?= htmlentities($recipe['name']) ?>" class="recipe-image">

        <div class="recipe-header">
            <form method="post" class="save-form">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="save-button1">+ Save Recipe</a>
                <?php else: ?>
                    <form method="post" action="save-recipe.php" class="save-form">
                        <input type="hidden" name="recipe_id" value="<?= $recipe['id'] ?>">
                        <button type="submit" name="fav" value="<?= $favExists ? 0 : 1 ?>" class="save-button">
                            <?= $favExists ? '★ Remove Favorite' : '+ Save Recipe' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </form>
            <h1><?= htmlentities($recipe['name']) ?></h1>
            <p><?= htmlentities($recipe['description']) ?></p>
            <div class="recipe-meta">
                <span>⏱ Prep: <?= $recipe['prep_time'] ?></span>
                <span>Cook: <?= $recipe['cook_time'] ?></span>
                <span>Serves: <?= $recipe['servings'] ?></span>
                <span>Categories: <?= implode(', ', $cats) ?></span>
            </div>
        </div>
    </section>

    <hr class="divider">

    <section class="ingredients">
        <h2>Ingredients</h2>
        <ul>
            <?php foreach ($ingredients as $i): ?>
                <li><?= htmlentities($i['quantity'] . ' ' . $i['ingredient']) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <hr class="divider">

    <section class="instructions">
        <h2>Instructions</h2>
        <ol>
            <?php foreach ($steps as $step): ?>
                <li><?= htmlentities($step['instruction']) ?> (<?= $step['duration'] ?> mins)</li>
            <?php endforeach; ?>
        </ol>
    </section>

    <hr class="divider">

    <section class="rating">
        <h2><?= empty($usersRating) ? "Rate this Recipe" : "Your Rating" ?></h2>

        <?php if ($avgRatings['no_of_users'] > 0): ?>
            <p>Average rating of <?= $avgRatings['no_of_users'] ?> users</p>
            <?php foreach ($ratingCategories as $cat): ?>
                <p><?= ucfirst($cat) ?>: <?= round($avgRatings['avg_' . $cat . '_score'], 1) ?> / 5</p>
            <?php endforeach; ?>
        <?php else: ?>
            <p>This recipe hasn't been rated yet.</p>
        <?php endif; ?>

        <form method="post" class="rating-form">
            <?php foreach ($ratingCategories as $cat): ?>
                <label>
                    <?= ucfirst($cat) ?>
                    <select name="<?= $cat ?>_score">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= $i == $selectedRatingScore[$cat] ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </label>
            <?php endforeach; ?>
            <button type="submit" name="rate" class="rate-button">Submit Rating</button>
        </form>
    </section>

    <?php if ($error): ?>
        <div class="error"><?= htmlentities($error) ?></div>
    <?php endif; ?>

    <div class="back-link">
        <a href="search.php">← Back to Search</a>
    </div>
</main>

<?php require 'includes/footer.php'; ?>
