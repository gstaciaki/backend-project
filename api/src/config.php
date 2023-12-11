<?php

$host = 'database';
$dbname = 'mydb';
$user = 'mydb_user'; 
$password = 'mydb_password'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    die();
}
