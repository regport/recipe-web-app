<?php
require 'includes/header.php';
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$recipeId = intval($_GET['id'] ?? 0);
//Fetch recipe
$stmtRecipe = $pdo -> prepare("SELECT * FROM recipes r where r.id=?");
$stmtRecipe -> execute([$recipeId]);
$recipe = $stmtRecipe->fetch();
//Fetch categories
$stmtCats = $pdo->prepare("SELECT c.name FROM recipe_categories rc join categories c on c.id = rc.category_id WHERE rc.recipe_id=?");
$stmtCats->execute([$recipeId]);
$catsResult = $stmtCats->fetchAll();
$cats = array_map(function($catResult) {
    return $catResult['name'];
},$catsResult);
//Fetch ingredients
$stmtIngredients= $pdo->prepare("SELECT ri.quantity as quantity, i.name as ingredient FROM recipe_ingredients ri join ingredients i on ri.ingredient_id = i.id where ri.recipe_id=?");
$stmtIngredients -> execute([$recipeId]);
$ingredients = $stmtIngredients->fetchAll();
//Fetch steps
$stmtSteps= $pdo->prepare("SELECT * FROM steps s where s.recipe_id=?");
$stmtSteps -> execute([$recipeId]);
$steps = $stmtSteps->fetchAll();
// Check if favourited
$stmtFavExists = $pdo->prepare("SELECT count(*) as favExists FROM favourites f where f.user_id=? and f.recipe_id=?");
$stmtFavExists->execute([$uid, $recipeId]);
$favExistsResult = $stmtFavExists->fetch();
$favExists= $favExistsResult['favExists'];

$ratingCategories = ['difficulty', 'aesthetics', 'taste'];
//Fetch average ratings
$stmtAvgRatings = $pdo->prepare("SELECT avg(r.difficulty_score) as avg_difficulty_score, avg(r.aesthetics_score) as avg_aesthetics_score, avg(r.taste_score) as avg_taste_score, count(user_id) as no_of_users FROM ratings r where r.recipe_id=?");
$stmtAvgRatings->execute([$recipeId]);
$avgRatings = $stmtAvgRatings->fetch();

//Fetch user's rating
$stmtUsersRating = $pdo->prepare("SELECT r.difficulty_score, r.aesthetics_score, r.taste_score FROM ratings r where r.recipe_id=? and r.user_id=?");
$stmtUsersRating->execute([$recipeId, $uid]);
$usersRating = $stmtUsersRating->fetch();

$isFavClicked = isset($_POST['fav']);
$isRateClicked = isset($_POST['rate']);


$selectedRatingScore['difficulty'] = !empty($usersRating) ? $usersRating['difficulty_score'] : 1;
$selectedRatingScore['aesthetics'] = !empty($usersRating) ? $usersRating['aesthetics_score'] : 1;
$selectedRatingScore['taste'] = !empty($usersRating) ? $usersRating['taste_score'] : 1;
$error = null;
    
// Handle POST for favorite toggle & to update rating
if($_SERVER['REQUEST_METHOD']==='POST') {
    try{
        if($isFavClicked) {
            //toggle favourite
            $addAsFav = $_POST['fav'];
            $queryUpdFav = $addAsFav ? "INSERT INTO favourites(user_id, recipe_id) values (?, ?)" : "DELETE FROM favourites WHERE user_id=? and recipe_id=?";
            $stmtUpdateFav = $pdo->prepare($queryUpdFav);
            $favUpdated = $stmtUpdateFav->execute([$uid, $recipeId]);
            if($favUpdated) {
                header("Location: recipe.php?id=$recipeId");
            }
        }
        if($isRateClicked) {
            $selectedRatingScore['difficulty'] = $_POST['difficulty_score'];
            $selectedRatingScore['aesthetics'] = $_POST['aesthetics_score'];
            $selectedRatingScore['taste'] = $_POST['taste_score'];
            //Insert or update rating
            if(empty($usersRating)) {
              $stmtAddRating = $pdo->prepare("INSERT INTO ratings (user_id, recipe_id, difficulty_score, aesthetics_score, taste_score) VALUES (?, ?, ?, ?, ?)");
              $stmtAddRating->execute([$uid, $recipeId, $selectedRatingScore['difficulty'], $selectedRatingScore['aesthetics'] , $selectedRatingScore['taste']]);
            } else {
              $stmtUpdRating = $pdo->prepare("Update ratings set difficulty_score=?, aesthetics_score=?, taste_score=? WHERE recipe_id=? and user_id=?");
              $stmtUpdRating->execute([$selectedRatingScore['difficulty'], $selectedRatingScore['aesthetics'] , $selectedRatingScore['taste'], $recipeId, $uid]);
            }
            header("Location: recipe.php?id=$recipeId");
        }
    } catch(Exception $e) {
        $error = "Failed to update last change.";
    }

}
?>
<article>
  <h1><?= htmlentities($recipe['name']) ?></h1>
  <img src="<?= htmlentities($recipe['image'])?>" alt="">
  <p>Prep: <?= $recipe['prep_time']?> | Cook: <?= $recipe['cook_time']?></p>
  <p>Servings: <?= $recipe['servings']?></p>
  <p>Categories: <?= implode(', ', $cats) ?></p>

  <h2>Ingredients</h2>
  <ul><?php foreach($ingredients as $i): ?>
    <li><?= htmlentities($i['quantity'].' '.$i['ingredient']) ?></li>
  <?php endforeach; ?></ul>

  <h2>Steps</h2>
  <ol><?php foreach($steps as $s): ?>
    <li><?= htmlentities($s['instruction'].' (duration: '.$s['duration'].' minutes)') ?></li>
  <?php endforeach; ?></ol>

  <form method="post">
    <button name="fav" value="<?= $favExists?0:1 ?>">
      <?= $favExists?'★ Remove Favorite':'☆ Add to Favourites' ?>
    </button>
  </form>
  <br>

  <h2>Average User Ratings</h2>
  <section>
        <?php if($avgRatings['no_of_users'] === 0): ?>
            <p>Not yet rated.</p>
        <?php else: ?>
            <p>Average rating of <?= $avgRatings['no_of_users'] ?> users</p>
            <?php foreach($ratingCategories as $ratingCategory): ?>
              <p><?= ucfirst($ratingCategory) ?>:<?= $avgRatings['avg_'.$ratingCategory.'_score'] ?> out of 5</p>
            <?php endforeach; ?>
        <?php endif; ?>
  </section>

  <h2><?= empty($usersRating) ? "Rate This Recipe" : "Your rating" ?></h2>
  <form method="post">
      <?php foreach($ratingCategories as $ratingCategory): ?>
        <label><?= ucfirst($ratingCategory)?>
          <select name="<?= $ratingCategory.'_score' ?>">
            <?php for($i=1;$i<=5;$i++): ?>
              <option value="<?= $i ?>" <?=  $i === $selectedRatingScore[$ratingCategory] ? "selected=\"selected\"" : ""?>><?= $i ?></option>
            <?php endfor; ?>
          </select>
        </label>
      <?php endforeach; ?>
      <br>
    <button type="submit" name="rate">Submit Ratings</button>
  </form>
  <br>
  <?php if($error): ?>
      <div class="error">
          <p><?= $error ?></p>
      </div>
  <?php endif; ?>
</article>
<?php require 'includes/footer.php'; ?>
