<?php

require_once 'config.php';
require_once 'common.php';

$pdo = dbConnection();

$nameValue = '';
$emailValue = '';
$commentsValue = '';

$errorName = '';
$errorEmail = '';
$errorComments = '';

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['comments'])) {
    $nameValue = $_POST['name'];
    $emailValue = $_POST['email'];
    $emailValue = $_POST['comments'];

    if (!$nameValue) {
        $errorName = 'Name is required!';
    }

    if (!$emailValue) {
        $errorEmail = 'Email is required!';
    } elseif (!filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
        $errorEmail = 'Email is invalid';
    }

    if (!$commentsValue) {
        $errorComments = 'Invalid comments!';
    }
}

if (isset($_POST['id']) && in_array($_POST['id'], $_SESSION['cart'])) {
    $index = array_search($_POST['id'], $_SESSION['cart']);
    array_splice($_SESSION['cart'], $index, 1);
}

if ($_SESSION['cart']) {
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $sql = "SELECT * FROM product WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($_SESSION['cart']);
    $products = $stmt->fetchAll(PDO::FETCH_OBJ);
} else {
    $products = [];
}

?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<nav>
    <a href="index.php"> Home </a>
</nav>
<?php
foreach ($products as $i => $product): ?>
    <div style="display: flex; width: 700px; margin: auto">
        <img src="<?= $product->img_path ?>" alt="product image" style="width: 150px; height: 150px">
        <div>
            <h1><?= $product->title; ?></h1>
            <p><?= $product->description; ?></p>
            <p><?= $product->price; ?></p>
        </div>
        <form action="cart.php" method="post">
            <input type="hidden" name="id" value="<?= $product->id; ?>">
            <input type="submit" value="<?= translate('Remove Fom Cart'); ?>">
        </form>
    </div>
<?php
endforeach; ?>
<form action="cart.php" method="post" style="width: 700px; margin: auto">
    <label for="name"><?= translate('Name :'); ?> </label>
    <input type="text" name="name" id="name" value="<?= $nameValue ?>"><br>

    <?php
    if ($errorName): ?>
        <p style="color: red"> <?= $errorEmail ?> </p> <br>
    <?php
    endif; ?>

    <label for="email"> <?= translate('Email :'); ?> </label>
    <input type="text" name="email" id="email"><br>

    <?php
    if ($errorEmail): ?>
        <p style="color: red"> <?= $errorEmail ?> </p> <br>
    <?php
    endif; ?>

    <label for="comments"> <?= translate('Comments :'); ?> </label>
    <textarea name="comments" id="comments" cols="30" rows="10"></textarea> <br>

    <?php
    if ($errorComments): ?>
        <p style="color: red"> <?= $errorComments ?> </p> <br>
    <?php
    endif; ?>

    <input type="submit" value="<?= translate('Check Out') ?>">
</form>
</body>
</html>