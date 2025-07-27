<?php
$pdo = new PDO('mysql:host=localhost;dbname=recipe;charset=utf8mb4', 'root', 'password');

$keyword = $_GET['keyword'] ?? '';
$category = $_GET['category'] ?? '';
$dietary = $_GET['dietary'] ?? '';

$sql = "
SELECT DISTINCT r.id, r.title, r.image_url, r.prep_time, r.cook_time, r.servings, a.name AS author
FROM recipes r
JOIN authors a ON r.author_id = a.id
LEFT JOIN recipe_categories rc ON r.id = rc.recipe_id
LEFT JOIN categories c ON rc.category_id = c.id
LEFT JOIN recipe_dietary rd ON r.id = rd.recipe_id
LEFT JOIN dietary d ON rd.dietary_id = d.id
LEFT JOIN recipe_ingredients ri ON r.id = ri.recipe_id
LEFT JOIN ingredients i ON ri.ingredient_id = i.id
WHERE 1=1
";

$params = [];

if (!empty($keyword)) {
    $sql .= " AND (r.title LIKE :keyword OR i.name LIKE :keyword)";
    $params[':keyword'] = '%' . $keyword . '%';
}

if (!empty($category)) {
    $sql .= " AND c.name = :category";
    $params[':category'] = $category;
}

if (!empty($dietary)) {
    $sql .= " AND d.name = :dietary";
    $params[':dietary'] = $dietary;
}

$sql .= " ORDER BY r.title";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
    
</head>
<body>
    <a href="index.php">&larr; Back to Search</a>
    <h1>Search Results</h1>

    <?php if (empty($recipes)): ?>
        <p>No recipes found.</p>
    <?php else: ?>
        <ul class="recipe-list">
            <?php foreach ($recipes as $r): ?>
                <li class="recipe-card">
                    <img src="<?= htmlspecialchars($r['image_url']) ?>" alt="<?= htmlspecialchars($r['title']) ?>" class="thumb">
                    <div class="details">
                        <h2><a href="recipe.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></a></h2>
                        <p><strong>By:</strong> <?= htmlspecialchars($r['author']) ?></p>
                        <p><strong>Prep:</strong> <?= htmlspecialchars($r['prep_time']) ?> |
                           <strong>Cook:</strong> <?= htmlspecialchars($r['cook_time']) ?> |
                           <strong>Servings:</strong> <?= htmlspecialchars($r['servings']) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
