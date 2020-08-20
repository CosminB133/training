<?php

require_once 'config.php';
require_once 'common.php';

$products = getAllProducts();

if ( isset( $_POST['id'] ) && in_array( $_POST['id'], $_SESSION['cart'] ) ) {
    $index = array_search($_POST['id'], $_SESSION['cart']);
    array_splice($_SESSION['cart'], $index, 1);
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
<?php foreach ($products as $i => $product):?>
    <?php if ( in_array($product->id, $_SESSION['cart'] ) ): ?>
        <div style="display: flex">
            <img src="<?= $product->img_path?>" alt="product image" style="width: 150px; height: 150px">
            <div>
                <h1><?= $product->title; ?></h1>
                <p><?= $product->description; ?></p>
            </div>
            <form action="cart.php" method="post">
                <input type="hidden" name="id" value="<?= $product->id; ?>">
                <input type="submit" value="Remove Fom Cart">
            </form>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
</body>
</html>