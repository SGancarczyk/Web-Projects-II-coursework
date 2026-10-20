<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$db = (new Database())->getConnection();

// rm recipe
if (isset($_POST['remove'])) {
    $rid = $_POST['recipe_id'];
    $stmt = $db->prepare("DELETE FROM UserFavorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$uid, $rid]);
}

// display fav rec.
$query = "SELECT r.recipe_id, r.title, r.image
          FROM Recipes r
          JOIN UserFavorites uf ON r.recipe_id = uf.recipe_id
          WHERE uf.user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$uid]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .card { border: 1px solid #ddd; padding: 10px; border-radius: 8px; text-align: center; }
        .card img { width: 100%; border-radius: 4px; }
    </style>
</head>
<body>
    <nav><a href="search.php">Back to Search</a> | <a href="logout.php">Logout</a></nav>
    <h2>My Favorite Recipes</h2>

    <?php if (empty($favorites)): ?>
        <p>No favorite recipes saved yet.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($favorites as $f): ?>
                <div class="card">
                    <img src="<?= $f['image'] ?>" alt="recipe">
                    <h4><?= $f['title'] ?></h4>
                    <a href="recipe.php?id=<?= $f['recipe_id'] ?>">View Details</a>
                    <form method="POST" style="margin-top:10px;">
                        <input type="hidden" name="recipe_id" value="<?= $f['recipe_id'] ?>">
                        <button name="remove" type="submit" style="color:red;">Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>