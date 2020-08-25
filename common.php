<?php

session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['auth'])) {
    $_SESSION['auth'] = false;
}

function translate($label)
{
    return $label;
}

function getAllProducts($pdo)
{
    $stmt = $pdo->prepare('SELECT * FROM product');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function redirect($location)
{
    header('Location: ' . $location . '.php');
    exit();
}

$dsn = 'mysql:host=' . DB_HOST . ';' . 'dbname=' . DB_NAME;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
} catch (PDOException $e) {
    return $e->getMessage();
}

