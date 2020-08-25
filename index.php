<?php

require_once 'config.php';
require_once 'common.php';


if (
    isset($_POST['id'])
    && !in_array($_POST['id'], $_SESSION['cart'])
    && validProductId($pdo, $_POST['id'])
) {
    array_push($_SESSION['cart'], $_POST['id']);
}

if ($_SESSION['cart']) {
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $sql = "SELECT * FROM product WHERE id NOT IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($_SESSION['cart']);
    $products = $stmt->fetchAll(PDO::FETCH_OBJ);
} else {
    $products = getAllProducts($pdo);
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
    <a href="cart.php"> <?= translate(' To cart '); ?> </a>
    <?php if ($_SESSION['auth']): ?>
        <a href="products.php"> <?= translate(' Products '); ?> </a>
    <?php else: ?>
        <a href="login.php"> <?= translate(' Login '); ?> </a>
    <?php endif; ?>
</nav>
<?php foreach ($products as $product): ?>
    <div style="display: flex; width: 700px; margin: auto">
        <img src="<?= $product->img_path; ?>" alt="product image" style="width: 150px; height: 150px">
        <div>
            <h1><?= $product->title; ?></h1>
            <p><?= $product->description; ?></p>
            <p><?= $product->price; ?></p>
        </div>
        <form action="index.php" method="post">
            <input type="hidden" name="id" value="<?= $product->id; ?>">
            <input type="submit" value="<?= translate('Add'); ?>">
        </form>
    </div>
<?php endforeach; ?>
</body>
</html>