<?php

require_once 'config.php';
require_once 'common.php';

$errorImgUrl = '';
$errorDescription = '';
$errorTitle = '';
$errorPrice = '';

$title = '';
$description = '';
$price = '';
$imgUrl = '';

if (!$_SESSION['auth']) {
    redirect('login');
}

if (isset($_POST['editId']) && !validProductId($pdo, $_POST['editId'])) {
    redirect('products');
}

if (
    isset($_POST['title'])
    && isset($_POST['description'])
    && isset($_POST['price'])
    && isset($_POST['imgUrl'])
) {
    $title = strip_tags($_POST['title']);
    $description = strip_tags($_POST['description']);
    $price = strip_tags($_POST['price']);
    $imgUrl = strip_tags($_POST['imgUrl']);

    if (!$imgUrl) {
        $errorImgUrl = 'Url is required';
    } elseif (!filter_var($imgUrl, FILTER_VALIDATE_URL)) {
        $errorImgUrl = 'Invalid Url';
    }

    if (!$description) {
        $errorDescription = 'Description is required';
    }

    if (!$price) {
        $errorPrice = 'Price is required';
    } elseif (
        !filter_var($price, FILTER_VALIDATE_FLOAT)
        || (float)$price < 0
    ) {
        $errorPrice = 'Enter an valid number';
    }

    if (!$title) {
        $errorTitle = 'Title is required';
    }

    if (!$errorDescription && !$errorImgUrl && !$errorTitle && !$errorPrice) {
        if (!isset($_POST['editId'])) {
            $stmt = $pdo->prepare(
                'INSERT INTO product(title, description, price, img_path) VALUES (:title, :description, :price, :imgUrl)'
            );
        } else {
            $stmt = $pdo->prepare(
                'UPDATE product SET title = :title, description = :description, price = :price, img_path = :imgUrl  WHERE id = :id'
            );
            $stmt->bindValue(':id', $_POST['editId'], PDO::PARAM_INT);
        }


        $stmt->bindValue(':title', strip_tags($title), PDO::PARAM_STR);
        $stmt->bindValue(':description', strip_tags($description), PDO::PARAM_STR);
        $stmt->bindValue(':price', strip_tags($price), PDO::PARAM_STR);
        $stmt->bindValue(':imgUrl', strip_tags($imgUrl), PDO::PARAM_STR);
        $success = $stmt->execute();

        if ($success) {
            header('Location: products.php');
            exit();
        }
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
    $stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$product) {
        redirect('products');
    }

    $title = $product->title;
    $description = $product->description;
    $price = $product->price;
    $imgUrl = $product->img_path;
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
    <a href="index.php">  <?= translate('Home') ?> </a>
    <a href="products.php"> <?= translate('Products') ?> </a>
    <a href="login.php"> <?= translate('Login') ?> </a>
</nav>

<form action="product.php" method="post">
    <label for="title"><?= translate('Title :'); ?> </label>
    <input type="text" name="title" id="title" value="<?= $title; ?>"><br>

    <?php if ($errorTitle): ?>
        <p style="color: red"> <?= $errorTitle ?> </p> <br>
    <?php endif; ?>

    <label for="description"><?= translate('Description :'); ?> </label>
    <textarea name="description" id="description" cols="30" rows="10"><?= $description; ?></textarea><br>

    <?php if ($errorDescription): ?>
        <p style="color: red"> <?= $errorDescription ?> </p> <br>
    <?php endif; ?>

    <label for="price"><?= translate('Price :'); ?> </label>
    <input type="text" name="price" id="price" value="<?= $price; ?>"><br>

    <?php if ($errorPrice): ?>
        <p style="color: red"> <?= $errorPrice ?> </p> <br>
    <?php endif; ?>

    <label for="imgUrl"><?= translate('Image path :'); ?> </label>
    <input type="text" name="imgUrl" id="imgUrl" value="<?= $imgUrl; ?>"><br>

    <?php if ($errorImgUrl): ?>
        <p style="color: red"> <?= $errorImgUrl ?> </p> <br>
    <?php endif; ?>

    <?php if (isset($_GET['id'])): ?>
        <input type="hidden" name="editId" value="<?= $_GET['id']; ?>">
    <?php endif; ?>

    <input type="submit" value="<?= isset($_GET['id']) ? translate('Edit') : translate('Add'); ?>">
</form>
</body>
</html>
