<?php
require 'includes/header.php';

$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
$catSelected = $_GET['cat'] ?? [];
$isSearchClicked = isset($_GET['search']);
$orderByVariations = [
    "recipe-name-ascending" => "name asc",
    "recipe-name-descending" => "name desc",
    "author-name-ascending" => "author asc",
    "author-name-descending" => "author desc",
    "prep-time-ascending" => "prep_time asc",
    "prep-time-descending" => "prep_time desc",
    "cook-time-ascending" => "cook_time asc",
    "cook-time-descending" => "cook_time desc",
    "servings-ascending" => "servings asc",
    "servings-descending" => "servings desc"
];
$orderBySelected = $_GET['orderBy'] ?? "recipe-name-ascending";

$ingredientsToSearch = trim($_GET['ingredient'] ?? '');
$nameToSearch = trim($_GET['name'] ?? '');
$maxPrepTime = trim($_GET['max_prep'] ?? '');
$maxCookTime = trim($_GET['max_cook'] ?? '');
$maxServings = trim($_GET['max_servings'] ?? '');
$author = trim($_GET['author'] ?? '');

$isSearchCriteriaSet = !empty($catSelected) || !empty($ingredientsToSearch) || !empty($nameToSearch)
    || !empty($maxPrepTime) || !empty($maxCookTime) || !empty($author) || !empty($maxServings);

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $isSearchCriteriaSet) {
    $whereClause = "";
    $parameterValues = [];
    $orderByClause = $orderByVariations[$orderBySelected];

    if (!empty($nameToSearch)) {
        $whereClause .= " AND lower(r.name) LIKE lower(?)";
        $parameterValues[] = "%$nameToSearch%";
    }

    if (!empty($catSelected)) {
        $whereClause .= " AND rc.category_id IN (" . str_repeat('?,', count($catSelected) - 1) . "?)";
        array_push($parameterValues, ...$catSelected);
    }

    if (!empty($author)) {
        $whereClause .= " AND lower(u.name) LIKE lower(?)";
        $parameterValues[] = "%$author%";
    }

    if (!empty($ingredientsToSearch)) {
        $whereClause .= " AND (";
        $ingredientsListToSearch = array_map('trim', explode(',', $ingredientsToSearch));
        $ingredientParameters = "(";
        foreach ($ingredientsListToSearch as $index=>$ingredient) {
            $whereClause .= " lower(i.name) LIKE lower(?)";
            $whereClause .= $index == sizeof($ingredientsListToSearch) -1 ? "" : " OR ";
            array_push($parameterValues, "%".$ingredient."%");
        }
        $whereClause .= " ) ";
    }

    if (!empty($maxCookTime)) {
        $whereClause .= " AND r.cook_time <= ?";
        $parameterValues[] = $maxCookTime;
    }

    if (!empty($maxPrepTime)) {
        $whereClause .= " AND r.prep_time <= ?";
        $parameterValues[] = $maxPrepTime;
    }

    if (!empty($maxServings)) {
        $whereClause .= " AND r.servings <= ?";
        $parameterValues[] = $maxServings;
    }

    $sql = "SELECT r.id, r.name, r.image, r.prep_time, r.cook_time, r.servings, u.name AS author
            FROM recipes r
            JOIN recipe_categories rc ON r.id = rc.recipe_id
            JOIN users u ON r.author_id = u.id
            JOIN recipe_ingredients ri ON r.id = ri.recipe_id
            JOIN ingredients i ON ri.ingredient_id = i.id
            WHERE 1=1 $whereClause
            GROUP BY r.id, r.name, r.prep_time, r.cook_time, r.servings, author
            ORDER BY $orderByClause";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parameterValues);
    $results = $stmt->fetchAll();
} else {
    $sqlAll = "SELECT r.id, r.name, r.image, r.prep_time, r.cook_time, r.servings, u.name AS author
            FROM recipes r
            JOIN recipe_categories rc ON r.id = rc.recipe_id
            JOIN users u ON r.author_id = u.id
            JOIN recipe_ingredients ri ON r.id = ri.recipe_id
            JOIN ingredients i ON ri.ingredient_id = i.id
            GROUP BY r.id, r.name, r.prep_time, r.cook_time, r.servings, author
            ORDER BY r.name limit 10";

    $results = $pdo->query($sqlAll)->fetchAll();
}
?>

<main>
    <section class="search-filter-section">
        <h1>Find Recipes</h1>
        <form method="get" id="search-form">
            <label>Recipe Name &nbsp;<input name="name" placeholder="Recipe name" value=<?= $nameToSearch ?? '' ?>></label>
            <br/>
            <fieldset>
                <legend><u>Categories</u></legend>
                <?php foreach ($cats as $c): ?>
                    <label>
                        <input class="category-checkbox" type="checkbox" name="cat[]" <?= in_array($c['id'], $catSelected, $strict = false) ? 'checked' : 'unchecked' ?> value="<?= $c['id'] ?>">
                        <?= $c['name'] ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>
            <div class="search-filter-section-fields">
                <label>Ingredient <input name="ingredient" placeholder="garlic,tomato" value="<?= $ingredientsToSearch ?? '' ?>" ></label>
                <label>Max Prep Time (mins) <input name="max_prep" placeholder="Prep Time in mins" value=<?= $maxPrepTime ?? '' ?> ></label>
                <label>Max Cook Time (mins) <input name="max_cook" placeholder="Cook Time in mins" value=<?= $maxCookTime ?? '' ?> ></label>
                <label>Max Servings <input name="max_servings" placeholder="Servings" value=<?= $maxServings ?? '' ?>></label>
                <label>Author Name <input name="author" placeholder="Author name" value=<?= $author ?? '' ?> ></label>
            </div>
            <br/>
            <label>Order by
                <select name="orderBy">
                    <?php foreach (array_keys($orderByVariations) as $orderByKey): ?>
                        <option value=<?= $orderByKey ?> <?= $orderByKey === $orderBySelected ? "selected=\"selected\"" : "" ?>><?= ucfirst(str_replace("-", " ", $orderByKey)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <br>
            <button type="submit" name="search">Search</button>
        </form>
    </section>
    <br />
    
        <section class="recipe-grid">
            <?php if (count($results) > 0): ?>
                <?php foreach ($results as $r): ?>
                    <div class="recipe-card">
                        <?php
                        $imageFile = !empty($r['image']) && file_exists("" . $r['image'])
                            ? "" . $r['image']
                            : "img/placeholder.jpg";
                        ?>
                        <img src="<?= $imageFile ?>" alt="<?= htmlentities($r['name']) ?>" />
                        <h3><?= htmlentities($r['name']) ?></h3>
                        <p>Prep: <?= htmlentities($r['prep_time']) ?> mins | Cook: <?= htmlentities($r['cook_time']) ?> mins</p>
                        <p>Servings: <?= htmlentities($r['servings']) ?></p>
                        <p>By <?= htmlentities($r['author']) ?></p>
                        <a href="recipe-detail.php?id=<?= $r['id'] ?>">View Recipe</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <h3 style="text-align:center;">No results found using that criteria.</h3>
            <?php endif; ?>
        </section>

    <!--section class="pagination">
        <a href="?page=1" class="page-link">« First</a>
        <a href="?page=2" class="page-link">‹ Prev</a>
        <span class="page-number current">3</span>
        <a href="?page=4" class="page-link">4</a>
        <a href="?page=5" class="page-link">5</a>
        <a href="?page=4" class="page-link">Next ›</a>
        <a href="?page=10" class="page-link">Last »</a>
    </section-->
</main>

<?php require 'includes/footer.php'; ?>