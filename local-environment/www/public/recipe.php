<?php
session_start();
require_once 'Database.php';
require 'vendor/autoload.php';
use GuzzleHttp\Client;

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: search.php");
    exit;
}

$id = $_GET['id'];
$uid = $_SESSION['user_id'];
$apiKey = "51b6d4206b4440efa72b6c64f918545d";
$client = new Client();


$res = $client->get("https://api.spoonacular.com/recipes/$id/information?apiKey=$apiKey");
$recipe = json_decode($res->getBody(), true);


if (isset($_POST['fav'])) {
    $db = (new Database())->getConnection();

    // localdb add rec
    $stmt = $db->prepare("INSERT IGNORE INTO Recipes (recipe_id, title, image, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$id, $recipe['title'], $recipe['image']]);

    // userlinkup
    $stmt = $db->prepare("INSERT IGNORE INTO UserFavorites (user_id, recipe_id, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$uid, $id]);
    $msg = "Added to favorites!";
}
?>
<!DOCTYPE html>
<html lang="en">
<body>
    <a href="search.php">Back</a>
    <h1><?= $recipe['title'] ?></h1>
    <img src="<?= $recipe['image'] ?>" width="300">
    <form method="POST"><button name="fav">Add to Favorites</button></form>
    <p><?= $msg ?? '' ?></p>
    <h3>Ingredients</h3>
    <ul><?php foreach ($recipe['extendedIngredients'] as $i): ?><li><?= $i['original'] ?></li><?php endforeach; ?></ul>
    <h3>Instructions</h3>
    <div><?= $recipe['instructions'] ?></div>
</body>
</html>