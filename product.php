<?php

require_once 'config.php';
require_once 'common.php';

if (!$_SESSION['auth']) {
    redirect('login');
}

if (!isset($_POST['editId']) || !validProductId($pdo, $_POST['editId'])) {
    redirect('products');
}

$data['title'] = '';
$data['description'] = '';
$data['price'] = '';
$data['imgUrl'] = '';

$reviews = [];
$errors = [];

if (isset($_POST['submit'])) {
    $data['title'] = strip_tags($_POST['title']);
    $data['description'] = strip_tags($_POST['description']);
    $data['price'] = strip_tags($_POST['price']);
    $data['imgUrl'] = strip_tags($_POST['imgUrl']);

    if (!$data['imgUrl']) {
        $errors['img'] = 'Url is required';
    } elseif (!filter_var($data['imgUrl'], FILTER_VALIDATE_URL)) {
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
        if (!isset($_POST['editId'])) {
            $stmt = $pdo->prepare(
                'INSERT INTO product(title, description, price, img_path) VALUES (:title, :description, :price, :imgUrl)'
            );
            $success = $stmt->execute([$data['title'], $data['description'], $data['price'], $data['imgUrl']]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE product SET title = ?, description = ?, price = ?, img_path = ?  WHERE id = ?'
            );
            $success = $stmt->execute([$data['title'], $data['description'], $data['price'], $data['imgUrl'], $_POST['editId']]);
        }

        if ($success) {
            redirect('products');
        }
    }
}

if (
    isset($_GET['id'])
) {
    $stmt = $pdo->prepare('SELECT * FROM product WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$product) {
        redirect('products');
    }

    $data['title'] = $product->title;
    $data['description'] = $product->description;
    $data['price'] = $product->price;
    $data['imgUrl'] = $product->img_path;

    if (
        isset($_POST['delCommentId'])
        && $_POST['delCommentId']
    ) {
        $stmt = $pdo->prepare('DELETE FROM reviews WHERE id = ?');
        $stmt->execute([$_POST['delCommentId']]);
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

<form action="product.php" method="post">
    <label for="title"><?= translate('Title :'); ?> </label>
    <input type="text" name="title" id="title" value="<?= $data['title']; ?>"><br>

    <?php if (array_key_exists('title', $errors)): ?>
        <p style="color: red"> <?= $errors['title'] ?> </p> <br>
    <?php endif; ?>

    <label for="description"><?= translate('Description :'); ?> </label>
    <textarea name="description" id="description" cols="30" rows="10"><?= $data['description']; ?></textarea><br>

    <?php if (array_key_exists('description', $errors)): ?>
        <p style="color: red"> <?= $errors['description'] ?> </p> <br>
    <?php endif; ?>

    <label for="price"><?= translate('Price :'); ?> </label>
    <input type="text" name="price" id="price" value="<?= $data['price']; ?>"><br>

    <?php if (array_key_exists('price', $errors)): ?>
        <p style="color: red"> <?= $errors['price'] ?> </p> <br>
    <?php endif; ?>

    <label for="imgUrl"><?= translate('Image path :'); ?> </label>
    <input type="text" name="imgUrl" id="imgUrl" value="<?= $data['imgUrl']; ?>"><br>

    <?php if (array_key_exists('img', $errors)): ?>
        <p style="color: red"> <?= $errors['img'] ?> </p> <br>
    <?php endif; ?>

    <?php if (isset($_GET['id'])): ?>
        <input type="hidden" name="editId" value="<?= $_GET['id']; ?>">
    <?php endif; ?>

    <input type="submit" name="submit" value="<?= isset($_GET['id']) ? translate('Edit') : translate('Add'); ?>">

</form>

<?php foreach ($reviews as $review): ?>
    <div>
        <h1><?= $review->rating; ?></h1>
        <p><?= $review->comment; ?></p>
    </div>

    <form action="product.php?id=<?= $product->id ?>" method="post">
        <input type="hidden" name="delCommentId" value="<?= $review->id ?>">
        <input type="submit" value="<?= translate('Delete') ?>">
    </form>
<?php endforeach; ?>
</body>
</html>
