<?php

session_start();

if ( !isset( $_SESSION['cart'] ) ) {
    $_SESSION['cart'] = array();
}

function getAllProducts() {
    $db_host = DB_HOST;
    $db_name = DB_NAME;
    $db_user = DB_USER;
    $db_pass = DB_PASS;

    $dsn = "mysql:host=$db_host;dbname=$db_name";

    try {
        $pdo = new PDO($dsn, $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
    }

    $stmt = $pdo->prepare('SELECT * FROM product');
    $stmt ->execute();

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
