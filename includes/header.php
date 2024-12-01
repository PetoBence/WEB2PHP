<?php
session_start();
include_once 'db.php';

// menu renderelési logika
include_once 'menu.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP webalkalmazás</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        header .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        header a {
            color: white;
            text-decoration: none;
            margin-left: 10px;
        }
        header a:hover {
            text-decoration: underline;
            color: #0056b3;
        }
        .welcome-text {
            color: white;
            font-weight: bold;
        }
        nav {
            background-color: #343a40;
            color: white;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            position: relative;
        }
        nav ul li a {
            display: block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
        }
        nav ul li a:hover {
            background-color: #495057;
        }
        nav ul li ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #495057;
            list-style: none;
            padding: 0;
            margin: 0;
            min-width: 200px;
            z-index: 1000;
        }
        nav ul li:hover > ul {
            display: block;
        }
        nav ul li ul li a {
            padding: 10px 20px;
        }
        nav ul li ul li a:hover {
            background-color: #6c757d;
        }
        
        /* Ensure login/register links are always visible in header */
        header .login-register {
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>PHP webalkalmazás érettségi (Név: PETŐ BENCE, NEPTUN: NYO49A)</h1>
            <div class="login-register">
                <?php if (isset($_SESSION['username'])): ?>
                    <p class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (Role: <?php echo htmlspecialchars($_SESSION['role']); ?>)</p>
                    <a href="auth/logout.php">Logout</a>
                <?php else: ?>
                    <a href="auth/login.php">Login</a> | <a href="auth/register.php">Regisztráció</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <nav>
        <ul>
            <?php renderMenu(); ?>  <!-- Calls the function to render the menu -->
        </ul>
    </nav>

</body>
</html>

