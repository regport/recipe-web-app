<?php
$pdo = new PDO('mysql:host=localhost;dbname=recipe;charset=utf8mb4', 'root', 'Sosonewsaleem5522');

$categories = $pdo->query("SELECT name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$dietary = $pdo->query("SELECT name FROM dietary ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recipe Finder</title>
    
</head>
<body>
    <h1>Recipe Finder</h1>
    <form action="search.php" method="get">
        <label for="keyword">Keyword (title or ingredient):</label>
        <input type="text" name="keyword" id="keyword" placeholder="e.g. garlic, pizza">

        <label for="category">Category:</label>
        <select name="category" id="category">
            <option value="">-- Any --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="dietary">Dietary:</label>
        <select name="dietary" id="dietary">
            <option value="">-- Any --</option>
            <?php foreach ($dietary as $d): ?>
                <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Search</button>
    </form>
</body>
</html>
