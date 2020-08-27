<?php

require_once 'config.php';
require_once 'common.php';

$data['name'] = '';
$data['comments'] = '';
$data['contact'] = '';

$errors = [];

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

if (isset($_POST['name']) && isset($_POST['contact']) && isset($_POST['comments'])) {
    $data['name'] = strip_tags($_POST['name']);
    $data['contact'] = strip_tags($_POST['contact']);
    $data['comments'] = strip_tags($_POST['comments']);

    if (!$data['name']) {
        $errors['name'] = 'Name is required!';
    }

    if (!$data['contact']) {
        $errors['contact'] = 'The contact details field is required!';
    }

    if (!$data['comments']) {
        $errors['comments'] = 'Comments are required!';
    }

    if (!$errors) {

        $stmt = $pdo->prepare('INSERT INTO orders(name, contact_details, comments) VALUES (?, ?, ?)');
        $stmt->execute([$data['name'], $data['contact'], $data['comments']]);

        $orderId = $pdo->lastInsertId();

        foreach ($products as $product) {
            $stmt = $pdo->prepare('INSERT INTO orders_products(order_id, product_id, price) VALUES (?, ?, ?)');
            $stmt->execute([$orderId, $product->id, $product->price]);
        }

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        ob_start();

        require_once 'email_template.php';

        $message = ob_get_clean();

        mail(MANAGER_EMAIL, 'Order', $message, $headers);

        redirect('index');
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
<nav>
    <a href="index.php">  <?= translate('Home') ?> </a>
    <?php if ($_SESSION['auth']): ?>
        <a href="products.php"> <?= translate('Products') ?> </a>
    <?php else: ?>
        <a href="login.php"> <?= translate('Login') ?> </a>
    <?php endif; ?>
</nav>
<?php foreach ($products as $product): ?>
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
<?php endforeach; ?>
<form action="cart.php" method="post" style="width: 700px; margin: auto">

    <label for="name"><?= translate('Name :'); ?> </label>
    <input type="text" name="name" id="name" value="<?= $data['name'] ?>"><br>
    <?php if (array_key_exists('name', $errors)): ?>
        <p style="color: red"> <?= $errors['name'] ?> </p> <br>
    <?php endif; ?>

    <label for="contact"> <?= translate('Contact Details :'); ?> </label>
    <input type="text" name="contact" id="contact" value="<?= $data['contact'] ?>"><br>
    <?php if (array_key_exists('contact', $errors)): ?>
        <p style="color: red"> <?= $errors['contact'] ?> </p> <br>
    <?php endif; ?>

    <label for="comments"> <?= translate('Comments :'); ?> </label>
    <textarea name="comments" id="comments" cols="30" rows="10"><?= $data['comments'] ?></textarea> <br>
    <?php if (array_key_exists('comments', $errors)): ?>
        <p style="color: red"> <?= $errors['comments'] ?> </p> <br>
    <?php endif; ?>

    <input type="submit" value="<?= translate('Check Out') ?>">
</form>
</body>
</html>