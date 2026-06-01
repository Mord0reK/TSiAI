<?php
date_default_timezone_set('Europe/Warsaw');

$host = 'mysql-db';
$dbname = 'TSiAI_Projekt';
$user = 'root';
$pass = 'rootpassword';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );
    $pdo->query('SET NAMES utf8');
    $pdo->query("SET time_zone = '+02:00'");
} catch (PDOException $e) {
    echo 'Połączenie nie mogło zostać utworzone: ' . $e->getMessage();
    exit();
}


