<?php
include_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $hashedPassword = hash('sha256', $password);

    try {
        // Új felhasználó hozzáadása (regisztrált felhasználó szerepkörben)
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'regisztrált felhasználó')");
        $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword,
        ]);
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>


