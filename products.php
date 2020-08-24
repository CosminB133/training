<?php

require_once 'config.php';
require_once 'common.php';

if (!$_SESSION['auth']) {
    header('Location: login.php');
    exit();
}

$pdo = dbConnection();

if (isset($_POST['delId']) && in_array($_POST['delId'], getAllIds($pdo))) {
    $stmt = $pdo->prepare('DELETE FROM product WHERE id = :id');
    $stmt->bindValue(':id', $_POST['delId'], PDO::PARAM_INT);
    $stmt->execute();
}

$stmt = $pdo->prepare('SELECT * FROM product');
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_OBJ);


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
    <a href="cart.php"> To cart </a>
</nav>
<?php
foreach ($products as $product): ?>
    <div style="display: flex; width: 700px; margin: auto">
        <img src="<?= $product->img_path; ?>" alt="product image" style="width: 150px; height: 150px">
        <div>
            <h1><?= $product->title; ?></h1>
            <p><?= $product->description; ?></p>
            <p><?= $product->price; ?></p>
        </div>
        <a href="product.php?id=<?= $product->id; ?>">Edit</a>
        <form action="products.php" method="post">
            <input type="hidden" name="delId" value="<?= $product->id; ?>">
            <input type="submit" value="<?= translate('Delete'); ?>">
        </form>
    </div>
<?php
endforeach; ?>
</body>
</html>