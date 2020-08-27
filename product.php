<?php

require_once 'config.php';
require_once 'common.php';

if (!$_SESSION['auth']) {
    redirect('login');
}

if (isset($_GET['id']) && !validProductId($pdo, $_GET['id'])) {
    redirect('products');
}

$reviews = [];
$errors = [];

if (isset($_POST['submit'])) {
    $data = array_map('strip_tags', $_POST);

    if (!$data['img_url']) {
        $errors['img'] = 'Url is required';
    } elseif (!filter_var($data['img_url'], FILTER_VALIDATE_URL)) {
        $errors['img'] = 'Invalid Url';
    }

    if (!$data['description']) {
        $errors['description'] = 'Description is required';
    }

    if (!$data['price']) {
        $errors['price'] = 'Price is required';
    } elseif (
        !filter_var($data['price'], FILTER_VALIDATE_FLOAT)
        || (float)$data['price'] < 0
    ) {
        $errors['price'] = 'Enter an valid number';
    }

    if (!$data['title']) {
        $errors['title'] = 'Title is required';
    }

    if (!$errors) {
        if (!isset($_GET['id'])) {
            $stmt = $pdo->prepare(
                'INSERT INTO product(title, description, price, img_path) VALUES (?, ?, ?, ?)'
            );
            $success = $stmt->execute([$data['title'], $data['description'], $data['price'], $data['img_url']]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE product SET title = ?, description = ?, price = ?, img_path = ?  WHERE id = ?'
            );
            $success = $stmt->execute([$data['title'], $data['description'], $data['price'], $data['img_url'], $_GET['id']]);
        }

        if ($success) {
            redirect('products');
        }
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM product WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$product) {
        redirect('products');
    }

    $data['title'] = $product->title;
    $data['description'] = $product->description;
    $data['price'] = $product->price;
    $data['img_url'] = $product->img_path;

    if (isset($_POST['del_comment_id']) && $_POST['del_comment_id']) {

        $stmt = $pdo->prepare('SELECT id FROM reviews WHERE id = ?');
        $stmt->execute([$_POST['del_comment_id']]);
        if ($stmt->fetch(PDO::FETCH_OBJ)){
            $stmt = $pdo->prepare('DELETE FROM reviews WHERE id = ?');
            $stmt->execute([$_POST['del_comment_id']]);
        }
    }

    $reviews = getReviews($pdo, $product->id);
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

<form action="product.php<?= (isset($_GET['id']))? '?id='.$_GET['id']: '' ?>" method="post">

    <label for="title"><?= translate('Title :'); ?> </label>
    <input type="text" name="title" id="title" value="<?= $data['title'] ?? '' ?>"><br>
    <?php if (array_key_exists('title', $errors)): ?>
        <p style="color: red"> <?= $errors['title'] ?> </p> <br>
    <?php endif; ?>

    <label for="description"><?= translate('Description :'); ?> </label>
    <textarea name="description" id="description" cols="30" rows="10"><?= $data['description'] ?? '' ?></textarea><br>
    <?php if (isset($errors['description'])): ?>
        <p style="color: red"> <?= $errors['description'] ?> </p> <br>
    <?php endif; ?>

    <label for="price"><?= translate('Price :'); ?> </label>
    <input type="text" name="price" id="price" value="<?= $data['price'] ?? '' ?>"><br>
    <?php if (isset($errors['price'])): ?>
        <p style="color: red"> <?= $errors['price'] ?> </p> <br>
    <?php endif; ?>

    <label for="img_url"><?= translate('Image path :'); ?> </label>
    <input type="text" name="img_url" id="img_url" value="<?= $data['img_url'] ?? '' ?>"><br>
    <?php if (isset($errors['img'])): ?>
        <p style="color: red"> <?= $errors['img'] ?> </p> <br>
    <?php endif; ?>

    <?php if (isset($_GET['id'])): ?>
        <input type="hidden" name="edit_id" value="<?= $_GET['id']; ?>">
    <?php endif; ?>

    <input type="submit" name="submit" value="<?= isset($_GET['id']) ? translate('Edit') : translate('Add'); ?>">

</form>

<?php foreach ($reviews as $review): ?>
    <div>
        <h1><?= $review->rating ?></h1>
        <p><?= $review->comment ?></p>
    </div>

    <form action="product.php?id=<?= $product->id ?>" method="post">
        <input type="hidden" name="del_comment_id" value="<?= $review->id ?>">
        <input type="submit" value="<?= translate('Delete') ?>">
    </form>
<?php endforeach; ?>
</body>
</html>
