<?php

require_once 'config.php';
require_once 'common.php';

if (!isset($_GET['id'])) {
    redirect('index');
}

$product = getProductById($pdo, $_GET['id']);

if (!$product) {
    redirect('index');
}

$data = [];
$errors = [];

if (isset($_POST['submit'])) {
    $data = array_map('strip_tags', $_POST);

    if (!isset($data['comment']) || !$data['comment']) {
        $errors['comment'] = 'Comment is required!';
    }

    if (!isset($data['rating']) || !$data['rating']){
        $errors['rating'] = 'PLease select an rating!';
    } else {
        $data['rating'] = (int) $data['rating'];
        if ($data['rating'] > 5 || $data['rating'] < 1) {
            $errors['rating'] = 'PLease insert a valid rating!';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO reviews (comment, rating, product_id) VALUES (?, ?, ?)');
        $stmt->execute([$data['comment'], $data['rating'], $product->id]);
    }
}
$reviews = getReviews($pdo, $product->id);

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
<div style="display: flex; width: 700px; margin: auto">
    <img src="<?= $product->img_path; ?>" alt="<?= translate('product image') ?>" style="width: 150px; height: 150px">
    <div>
        <h1><?= $product->title; ?></h1>
        <p><?= $product->description; ?></p>
        <p><?= $product->price; ?></p>
    </div>
</div>
<form action="review.php?id=<?= $product->id; ?>" method="post">
    <select name="rating" id="rating">
        <option value="1"><?= translate('1') ?></option>
        <option value="2"><?= translate('2') ?></option>
        <option value="3"><?= translate('3') ?></option>
        <option value="4"><?= translate('4') ?></option>
        <option value="5"><?= translate('5') ?></option>
    </select>
    <?php if (isset($errors['rating'])): ?>
        <p style="color: red"><?= $errors['rating'] ?></p>
    <?php endif; ?>
    <input type="text" name="comment" placeholder="<?= translate('Leave your comments here'); ?>">
    <?php if (isset($errors['comment'])): ?>
        <p style="color: red"><?= $errors['comment'] ?></p>
    <?php endif; ?>
    <input type="submit" name="submit" value="<?= translate('Submit review'); ?>">
</form>

<?php foreach ($reviews as $review): ?>
    <div>
        <h1><?= $review->rating; ?></h1>
        <p><?= $review->comment; ?></p>
    </div>
<?php endforeach; ?>
</body>
</html>