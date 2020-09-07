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

    if (!isset($data['description']) || !$data['description']) {
        $errors['description'] = 'Description is required';
    }

    if (!isset($data['price']) || !$data['price']) {
        $errors['price'] = 'Price is required';
    } elseif (
        !isset($data['price'])
        || !filter_var($data['price'], FILTER_VALIDATE_FLOAT)
        || (float)$data['price'] < 0
    ) {
        $errors['price'] = 'Enter an valid number';
    }

    if (isset($data['title']) || !$data['title']) {
        $errors['title'] = 'Title is required';
    }

    if (isset($_FILES['img']) && !$_FILES['img']['error']) {
        $allowedExt = [
            'jpg' => 'image/jpg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png'
        ];

        $ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));

        if (!isset($allowed_ext[$ext])) {
            $errors['img'] = 'Only JPG, JPEG, PNG, GIF and PNG files are allowed!';
        }

        if (!in_array($_FILES['img']['type'], $allowedExt)) {
            $errors['img'] = 'Please try again.';
        }
    } else {
        $errors['img'] = 'An valid image is required!';
    }


    if (!$errors) {
        if (!isset($_GET['id'])) {
            $stmt = $pdo->prepare(
                'INSERT INTO product(title, description, price) VALUES (?, ?, ?)'
            );
            $success = $stmt->execute([$data['title'], $data['description'], $data['price']]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE product SET title = ?, description = ?, price = ? WHERE id = ?'
            );
            $success = $stmt->execute(
                [$data['title'], $data['description'], $data['price'], $_GET['id']]
            );
        }

        if ($success) {
            $imgId = isset($_GET['id']) ? $_GET['id'] : $pdo->lastInsertId();
            move_uploaded_file(
                $_FILES['img']['tmp_name'],
                SITE_ROOT . '/img/' . $imgId
            );
            redirect('products');
        }
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM product WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_OBJ);

    $data['title'] = $product->title;
    $data['description'] = $product->description;
    $data['price'] = $product->price;

    if (isset($_POST['del_comment_id']) && $_POST['del_comment_id']) {
        $stmt = $pdo->prepare('SELECT id FROM reviews WHERE id = ?');
        $stmt->execute([$_POST['del_comment_id']]);
        if ($stmt->fetch(PDO::FETCH_OBJ)) {
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

<form action="product.php<?= (isset($_GET['id'])) ? '?id=' . $_GET['id'] : '' ?>"
      method="post"
      enctype="multipart/form-data">

    <label for="title"><?= translate('Title :'); ?> </label>
    <input type="text" name="title" id="title" value="<?= $data['title'] ?? '' ?>"><br>
    <?php
    if (array_key_exists('title', $errors)): ?>
        <p style="color: red"> <?= $errors['title'] ?> </p> <br>
    <?php
    endif; ?>

    <label for="description"><?= translate('Description :'); ?> </label>
    <textarea name="description" id="description" cols="30" rows="10"><?= $data['description'] ?? '' ?></textarea><br>
    <?php
    if (isset($errors['description'])): ?>
        <p style="color: red"> <?= $errors['description'] ?> </p> <br>
    <?php
    endif; ?>

    <label for="price"><?= translate('Price :'); ?> </label>
    <input type="text" name="price" id="price" value="<?= $data['price'] ?? '' ?>"><br>
    <?php
    if (isset($errors['price'])): ?>
        <p style="color: red"> <?= $errors['price'] ?> </p> <br>
    <?php
    endif; ?>

    <label for="img"><?= translate('Image :'); ?> </label>
    <input type="file" name="img" id="img"><br>
    <?php
    if (isset($errors['img'])): ?>
        <p style="color: red"> <?= $errors['img'] ?> </p> <br>
    <?php
    endif; ?>

    <?php
    if (isset($_GET['id'])): ?>
        <input type="hidden" name="edit_id" value="<?= $_GET['id']; ?>">
    <?php
    endif; ?>

    <input type="submit" name="submit" value="<?= isset($_GET['id']) ? translate('Edit') : translate('Add'); ?>">

</form>

<?php
foreach ($reviews as $review): ?>
    <div>
        <h1><?= $review->rating ?></h1>
        <p><?= $review->comment ?></p>
    </div>

    <form action="product.php?id=<?= $product->id ?>" method="post">
        <input type="hidden" name="del_comment_id" value="<?= $review->id ?>">
        <input type="submit" value="<?= translate('Delete') ?>">
    </form>
<?php
endforeach; ?>
</body>
</html>
