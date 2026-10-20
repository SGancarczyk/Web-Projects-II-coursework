<?php
require_once 'Database.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{9,}$/', $pass)) {
        $errors['pass'] = "Min 9 chars, 1 uppercase, 1 number.";
    }

    if (empty($errors)) {
        $db = (new Database())->getConnection();
        $sql = "INSERT INTO Users (email, password, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
        $stmt = $db->prepare($sql);
        $hashed = password_hash($pass, PASSWORD_BCRYPT);

        if ($stmt->execute([$email, $hashed])) {
            header("Location: login.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Register</title></head>
<body>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <p style="color:red"><?= $errors['email'] ?? '' ?></p>
        <input type="password" name="password" placeholder="Password" required>
        <p style="color:red"><?= $errors['pass'] ?? '' ?></p>
        <button type="submit">Register</button>
    </form>
</body>
</html>