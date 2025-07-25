<?php
require 'db.php';

// Retrieve filters from URL
$keyword = $_GET['keyword'] ?? '';
$category = $_GET['category'] ?? '';

// Base query
$sql = "SELECT * FROM recipes WHERE 1=1";
$params = [];

// Add filters
if (!empty($keyword)) {
    $sql .= " AND (title LIKE ? OR ingredients LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

// Execute query
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Search Results</h1>
    <a href="index.php">← Back to Search</a>

    <?php if (count($results) > 0): ?>
        <ul>
        <?php foreach ($results as $recipe): ?>
            <li>
                <strong><?= htmlspecialchars($recipe['title']) ?></strong><br>
                Category: <?= htmlspecialchars($recipe['category']) ?><br>
                Ingredients: <?= nl2br(htmlspecialchars($recipe['ingredients'])) ?><br>
                Steps: <?= nl2br(htmlspecialchars($recipe['steps'])) ?><br>
                Time per Step: <?= htmlspecialchars($recipe['time_per_step']) ?> minutes
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No recipes found.</p>
    <?php endif; ?>
</body>
</html>
=======
<?php
require 'db.php';

// Retrieve filters from URL
$keyword = $_GET['keyword'] ?? '';
$category = $_GET['category'] ?? '';

// Base query
$sql = "SELECT * FROM recipes WHERE 1=1";
$params = [];

// Add filters
if (!empty($keyword)) {
    $sql .= " AND (title LIKE ? OR ingredients LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

// Execute query
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Search Results</h1>
    <a href="index.php">← Back to Search</a>

    <?php if (count($results) > 0): ?>
        <ul>
        <?php foreach ($results as $recipe): ?>
            <li>
                <strong><?= htmlspecialchars($recipe['title']) ?></strong><br>
                Category: <?= htmlspecialchars($recipe['category']) ?><br>
                Ingredients: <?= nl2br(htmlspecialchars($recipe['ingredients'])) ?><br>
                Steps: <?= nl2br(htmlspecialchars($recipe['steps'])) ?><br>
                Time per Step: <?= htmlspecialchars($recipe['time_per_step']) ?> minutes
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No recipes found.</p>
    <?php endif; ?>
</body>
</html>
>>>>>>> Stashed changes
