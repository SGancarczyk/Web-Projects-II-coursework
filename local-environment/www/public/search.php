<?php
session_start();
require_once 'Database.php';
require 'vendor/autoload.php';
use GuzzleHttp\Client;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$results = [];
if (isset($_GET['query'])) {
    $client = new Client();
    $apiKey = "51b6d4206b4440efa72b6c64f918545d";
    $q = urlencode($_GET['query']);

    try {
        $res = $client->get("https://api.spoonacular.com/recipes/complexSearch?apiKey=$apiKey&query=$q");
        $data = json_decode($res->getBody(), true);
        $results = $data['results'] ?? [];
    } catch (Exception $e) {
        $error = "API Error.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .card { border: 1px solid #ddd; padding: 10px; text-align: center; border-radius: 8px; }
        .card img { width: 100%; border-radius: 5px; }
    </style>
</head>
<body>
    <nav><a href="favorites.php">Favorites</a> | <a href="logout.php">Logout</a></nav>
    <form method="GET">
        <input type="text" name="query" placeholder="Find a recipe..." required>
        <button type="submit">Search</button>
    </form>
    <div class="grid">
        <?php foreach ($results as $r): ?>
            <div class="card">
                <img src="<?= $r['image'] ?>" alt="recipe">
                <h4><?= $r['title'] ?></h4>
                <a href="recipe.php?id=<?= $r['id'] ?>">Details</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>