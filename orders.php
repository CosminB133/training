<?php

require_once 'config.php';
require_once 'common.php';

$stmt = $pdo->prepare(
    'SELECT orders.id, orders.creation_date, orders.name, orders.contact_details, orders.comments, SUM(price) AS price
     FROM orders LEFT JOIN orders_products ON orders.id = orders_products.order_id 
     GROUP by orders.id, orders.creation_date, orders.name, orders.contact_details, orders.comments'
);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_OBJ);

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
<?php foreach ($orders as $order): ?>
    <div style="width: 700px; margin: auto; border: 1px black solid">
        <p><?= $order->creation_date; ?></p>
        <p><?= $order->name; ?></p>
        <p><?= $order->comments; ?></p>
        <p><?= $order->price; ?></p>
        <a href="order.php?id=<?= $order->id; ?>">View</a>
    </div>
<?php endforeach; ?>
</body>
</html>