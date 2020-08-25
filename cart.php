<?php

require_once 'config.php';
require_once 'common.php';

$nameValue = '';
$emailValue = '';
$commentsValue = '';

$errorName = '';
$errorContact = '';
$errorComments = '';

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
    $nameValue = $_POST['name'];
    $contactValue = $_POST['contact'];
    $commentsValue = $_POST['comments'];

    if (!$nameValue) {
        $errorName = 'Name is required!';
    }

    if (!$contactValue) {
        $errorContact = 'The contact details field is required!';
    }

    if (!$commentsValue) {
        $errorComments = 'Comments are required!';
    }

    if
    (
        !$errorContact
        && !$errorName
        && !$errorComments
    ) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        $message = '<html><body>';
        $message .= '<p>Name = ' . strip_tags($nameValue). '</p>';
        $message .= '<p>Contact = ' . strip_tags($contactValue) . '</p>';
        $message .= '<p> Comments = ' . strip_tags($commentsValue) . '</p>';

        foreach ($products as $product) {
            $message .= '<div style="display: flex; width: 700px; margin: auto">';
            $message .= '<img src="' . strip_tags($product->img_path) . '" alt="product image" style="width: 150px; height: 150px">';
            $message .= '<div>';
            $message .= '<h1>' . strip_tags($product->title) . '</h1>';
            $message .= '<p>' . strip_tags($product->description) . '</p>';
            $message .= '<p>' . strip_tags($product->price) . '</p>';
            $message .= '</div>';
        }

        $message .= '</body></html>';

        if (mail(MANAGER_EMAIL, 'Order', $message, $headers)) {
            redirect('index');
        }
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
<?php foreach ($products as $i => $product): ?>
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
    <input type="text" name="name" id="name" value="<?= $nameValue ?>"><br>

    <?php if ($errorName): ?>
        <p style="color: red"> <?= $errorContact ?> </p> <br>
    <?php endif; ?>

    <label for="contact"> <?= translate('Contact Details :'); ?> </label>
    <input type="text" name="contact" id="contact" value="<?= $commentsValue ?>"><br>

    <?php if ($errorContact): ?>
        <p style="color: red"> <?= $errorContact ?> </p> <br>
    <?php endif; ?>

    <label for="comments"> <?= translate('Comments :'); ?> </label>
    <textarea name="comments" id="comments" cols="30" rows="10" value="<?= $commentsValue ?>"></textarea> <br>

    <?php if ($errorComments): ?>
        <p style="color: red"> <?= $errorComments ?> </p> <br>
    <?php endif; ?>

    <input type="submit" value="<?= translate('Check Out') ?>">
</form>
</body>
</html>