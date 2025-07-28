<?php
require 'includes/header.php';
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = $_SESSION['user_id'];

$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
$catSelected = $_GET['cat'] ?? [];
$isSearchClicked = isset($_GET['search']);
$orderByVariations = ["recipe-name-ascending" => "name asc", "recipe-name-descending" => "name desc", "author-name-ascending" => "author asc", "author-name-descending" => "author desc", "prep-time-ascending" => "prep_time asc", "prep-time-descending" => "prep_time desc", "cook-time-ascending" => "cook_time asc", "cook-time-descending" => "cook_time desc", "servings-ascending" => "servings asc", "servings-descending" => "servings desc"];
$orderBySelected = isset($_GET['orderBy']) ? $_GET['orderBy'] : "recipe-name-ascending";

$ingredientsToSearch = isset($_GET['ingredient']) ? trim(htmlentities($_GET['ingredient'])) : '';
$nameToSearch = isset($_GET['name']) ? trim(htmlentities($_GET['name'])) : '';
$maxPrepTime = isset($_GET['max_prep']) ? trim(htmlentities($_GET['max_prep'])) : '';
$maxCookTime = isset($_GET['max_cook']) ? trim(htmlentities($_GET['max_cook'])) : '';
$maxServings = isset($_GET['max_servings']) ? trim(htmlentities($_GET['max_servings'])) : '';
$author = isset($_GET['author']) ? trim(htmlentities($_GET['author'])) : '';
$isSearchCriteriaSet = !empty($catSelected) || !empty($ingredientsToSearch) || !empty($nameToSearch) || !empty($maxPrepTime) || !empty($maxCookTime) || !empty($author) || !empty($maxServings);

if($_SERVER['REQUEST_METHOD']==='GET' && $isSearchCriteriaSet) {
    //Fetch all recipes matching search criteria
    $whereClause = "";
    $parameterValues = [];
    $orderByClause = $orderByVariations[$orderBySelected];
    
    if(!empty($nameToSearch)) {
        $whereClause .= " and lower(r.name) like lower(?)";
        array_push($parameterValues, "%".$nameToSearch."%");
    }

    if(!empty($catSelected)) {
        $whereClause .= " and rc.category_id in (". str_repeat('?,', count($catSelected) - 1) . "?)"; // placeholders
        array_push($parameterValues, ...$catSelected);
    }

    if(!empty($author)) {
        $whereClause .= " and lower(u.name) like lower(?)";
        array_push($parameterValues, "%".$author."%");
    }

    if(!empty($ingredientsToSearch)) {
        $whereClause .= " and lower(i.name) like lower(?)";
        array_push($parameterValues, "%".$ingredientsToSearch."%");
    }
    
    if(!empty($maxCookTime)) {
        $whereClause .= " and lower(r.cook_time) like lower(?)";
        array_push($parameterValues, "%".$maxCookTime."%");
    }

    if(!empty($maxPrepTime)) {
        $whereClause .= " and lower(r.prep_time) like lower(?)";
        array_push($parameterValues, "%".$maxPrepTime."%");
    }

    if(!empty($maxServings)) {
        $whereClause .= " and lower(r.servings) like lower(?)";
        array_push($parameterValues, "%".$maxServings."%");
    }

    $sql= "SELECT
        r.id, r.name, r.prep_time, r.cook_time, r.servings, u.name as author
    FROM
        recipes r
    join recipe_categories rc on
        r.id = rc.recipe_id
    join users u on
        r.author_id = u.id
    join recipe_ingredients ri on
        r.id = ri.recipe_id
    join ingredients i on 
        ri.ingredient_id = i.id
    where 1 = 1 $whereClause 
    group by r.id, r.name, r.prep_time, r.cook_time, r.servings, author
    order by $orderByClause";

    $results =[];
    if(sizeof($parameterValues)>0) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($parameterValues);
        $results = $stmt->fetchAll();
    }
}

?>
<h1>Find Recipes</h1>
<form method="get" id="search-form">
  <label>Recipe Name <input name="name" placeholder="Recipe name" value=<?=$nameToSearch ?? ''?>></label>
  <fieldset>
    <legend>Categories</legend>
    <?php foreach($cats as $c): ?>
      <label>
        <input class="category-checkbox" type="checkbox" name="cat[]" <?=in_array($c['id'], $catSelected, $strict=false) ? 'checked': 'unchecked'?> value="<?=$c['id']?>"> 
        <?=$c['name']?>
    </label>
    <?php endforeach; ?>
  </fieldset>
  <br/>
  <label>Ingredient <input name="ingredient" value=<?=$ingredientsToSearch ?? ''?>></label>
  <label>Max Prep Time <input name="max_prep" value=<?= $maxPrepTime ?? '' ?>></label>
  <label>Max Cook Time <input name="max_cook" value=<?= $maxCookTime ?? '' ?>></label>
  <label>Max Servings <input name="max_servings" value=<?= $maxServings ?? '' ?>></label>
  <label>Author Name <input name="author" value=<?= $author ?? '' ?>></label>
  <label>Order by
    <select name="orderBy">
        <?php foreach(array_keys($orderByVariations) as $orderByKey): ?>
            <option value=<?= $orderByKey ?> <?= $orderByKey === $orderBySelected ? "selected=\"selected\"" : ""?>><?= ucfirst(str_replace("-", " ", $orderByKey)) ?></option>
        <?php endforeach; ?>
    </select>
  </label>
  <br>
  <button type="submit" name="search">Search</button>
</form>
<br/>

<?php if($isSearchClicked && $isSearchCriteriaSet) : ?>
    <h1>Results</h1>
    <div class="result-list">
    <?php foreach($results as $r): ?>
        <a href="recipe.php?id=<?= $r['id'] ?>"><?= htmlentities($r['name']) ?>
        (Prep:<?= htmlentities($r['prep_time'])?>, Cook:<?= htmlentities($r['cook_time'])?>, Servings:<?= htmlentities($r['servings']) ?>, Author: <?= htmlentities($r['author'])?>)
        </a>
    <?php endforeach; ?>
    <?php if(empty($results)) : ?>
        <h3>Sorry, no match found using that criteria</h3>
    <?php endif; ?>
    </div>
<?php endif; ?>
<?php require 'includes/footer.php'; ?>
