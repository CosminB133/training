<?php

require_once 'config.php';
require_once 'common.php';

if (!isset($_GET['id'])) {
    redirect('orders');
}

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$_GET['id']]);
$order = $stmt->fetch(PDO::FETCH_OBJ);

$stmt = $pdo->prepare(
    'SELECT img_path, title, description, orders_products.price  
FROM orders_products INNER JOIN product ON product.id = orders_products.product_id 
WHERE orders_products.order_id = ?'
);
$stmt->execute([$_GET['id']]);
$products = $stmt->fetchAll(PDO::FETCH_OBJ);


if (!$order) {
    redirect('orders');
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

<p><?= $order->creation_date; ?></p>
<p><?= $order->name; ?></p>
<p><?= $order->comments; ?></p>

<?php foreach ($products as $product): ?>
    <div style="display: flex; width: 700px; margin: auto">
        <img src="<?= $product->img_path; ?>" alt="product image" style="width: 150px; height: 150px">
        <div>
            <h1><?= $product->title; ?></h1>
            <p><?= $product->description; ?></p>
            <p><?= $product->price; ?></p>
        </div>
    </div>
<?php endforeach; ?>


</body>
</html>
