<?php

require_once 'config.php';
require_once 'common.php';

if (!$_SESSION['auth']) {
    header('Location: login.php');
    exit();
}

$pdo = dbConnection();

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
    $stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$product) {
        header('Location: products.php');
        exit();
    }

    $title = $product->title;
    $description = $product->description;
    $price = $product->price;
    $imgPath = $product->img_path;
} else {
    $title = '';
    $description = '';
    $price = '';
    $imgPath = '';
}

if (
    isset($_POST['title'])
    &&isset($_POST['description'])
    &&isset($_POST['price'])
    &&isset($_POST['imgPath'])
) {
    if (!isset($_POST['editId'])) {
        $stmt = $pdo->prepare('INSERT INTO product(title, description, price, img_path) VALUES (:title, :description, :price, :imgPath)');
    } else {
        $stmt = $pdo->prepare('UPDATE product SET title = :title, description = :description, price = :price, img_path = :imgPath  WHERE id = :id');
        $stmt->bindValue(':id', $_POST['editId'], PDO::PARAM_INT);
    }
    $stmt->bindValue(':title', $_POST['title'], PDO::PARAM_STR);
    $stmt->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
    $stmt->bindValue(':price', $_POST['price'], PDO::PARAM_STR);
    $stmt->bindValue(':imgPath', $_POST['imgPath'], PDO::PARAM_STR);
    $success = $stmt->execute();

    if ($success) {
        header('Location: products.php');
        exit();
    }


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
<form action="product.php" method="post">
    <label for="title"><?= translate('Title :'); ?> </label>
    <input type="text" name="title" id="title" value="<?= $title; ?>"><br>

    <label for="description"><?= translate('Description :'); ?> </label>
    <textarea name="description" id="description" cols="30" rows="10"><?= $description; ?></textarea><br>

    <label for="price"><?= translate('Price :'); ?> </label>
    <input type="number" name="price" id="price" value="<?= $price; ?>"><br>

    <label for="imgPath"><?= translate('Image path :'); ?> </label>
    <input type="text" name="imgPath" id="imgPath" value="<?= $imgPath; ?>"><br>

    <?php
    if (isset($_GET['id'])): ?>
        <input type="hidden" name="editId" value="<?= $_GET['id']; ?>">
    <?php
    endif; ?>

    <input type="submit" value="<?= isset($_GET['id'])? translate('Edit') : translate('Add') ; ?>">
</form>
</body>
</html>
