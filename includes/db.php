<?php

//adatbázis kapcsolat létrehozássa
$host = 'mysql.nethely.hu';
$dbname = 'nyo49a';
$username = 'nyo49a'@'185.187.75.18';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
