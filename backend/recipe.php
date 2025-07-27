<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid recipe ID.');
}

$recipeId = (int) $_GET['id'];

$pdo = new PDO('mysql:host=localhost;dbname=recipe;charset=utf8mb4', 'root', 'password');

// Fetch main recipe
$stmt = $pdo->prepare("
    SELECT r.title, r.image_url, r.prep_time, r.cook_time, r.servings, a.name AS author
    FROM recipes r
    JOIN authors a ON r.author_id = a.id
    WHERE r.id = ?
");
$stmt->execute([$recipeId]);
$recipe = $stmt->fetch();

if (!$recipe) {
    die('Recipe not found.');
}

// Fetch categories
$categories = $pdo->prepare("
    SELECT c.name FROM categories c
    JOIN recipe_categories rc ON c.id = rc.category_id
    WHERE rc.recipe_id = ?
");
$categories->execute([$recipeId]);
$categoryList = $categories->fetchAll(PDO::FETCH_COLUMN);

// Fetch dietary
$dietary = $pdo->prepare("
    SELECT d.name FROM dietary d
    JOIN recipe_dietary rd ON d.id = rd.dietary_id
    WHERE rd.recipe_id = ?
");
$dietary->execute([$recipeId]);
$dietaryList = $dietary->fetchAll(PDO::FETCH_COLUMN);

// Fetch ingredients
$ingredients = $pdo->prepare("
    SELECT i.name, ri.quantity
    FROM recipe_ingredients ri
    JOIN ingredients i ON ri.ingredient_id = i.id
    WHERE ri.recipe_id = ?
");
$ingredients->execute([$recipeId]);
$ingredientList = $ingredients->fetchAll();

// Fetch steps
$steps = $pdo->prepare("
    SELECT step_number, instruction, time_minutes
    FROM steps
    WHERE recipe_id = ?
    ORDER BY step_number
");
$steps->execute([$recipeId]);
$stepList = $steps->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($recipe['title']) ?> - Details</title>
    
</head>
<body>
    <a href="index.php">&larr; Back to Search</a>

    <div class="recipe-detail">
        <h1><?= htmlspecialchars($recipe['title']) ?></h1>
        <img src="<?= htmlspecialchars($recipe['image_url']) ?>" alt="<?= htmlspecialchars($recipe['title']) ?>" class="large-img">

        <p><strong>By:</strong> <?= htmlspecialchars($recipe['author']) ?></p>
        <p><strong>Prep time:</strong> <?= htmlspecialchars($recipe['prep_time']) ?> |
           <strong>Cook time:</strong> <?= htmlspecialchars($recipe['cook_time']) ?> |
           <strong>Servings:</strong> <?= htmlspecialchars($recipe['servings']) ?></p>

        <p><strong>Categories:</strong> <?= implode(', ', $categoryList) ?></p>
        <p><strong>Dietary:</strong> <?= implode(', ', $dietaryList) ?></p>

        <h2>Ingredients</h2>
        <ul>
            <?php foreach ($ingredientList as $ing): ?>
                <li><?= htmlspecialchars($ing['quantity']) ?> - <?= htmlspecialchars($ing['name']) ?></li>
            <?php endforeach; ?>
        </ul>

        <h2>Steps</h2>
        <ol>
            <?php foreach ($stepList as $step): ?>
                <li><?= htmlspecialchars($step['instruction']) ?> (<?= $step['time_minutes'] ?> min)</li>
            <?php endforeach; ?>
        </ol>
    </div>
</body>
</html>
