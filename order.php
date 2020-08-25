<?php
require_once 'config.php';
require_once 'common.php';

if (!isset($_GET['id'])) {
    redirect('orders');
}

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = :id');
$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_OBJ);

//if (!$order) {
//    redirect('orders');
//}



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
<a href="order.php?id= <?= $order->id; ?>">View</a>


</body>
</html>
