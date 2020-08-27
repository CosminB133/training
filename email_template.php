<html>
<body>
<p>Name = <?= $data['name'] ?></p>
<p>Contact = <?= $data['value'] ?></p>
<p> Comment = <?= $data['comments'] ?></p>
<?php foreach ($products as $product): ?>
<div style="display: flex; width: 700px; margin: auto">
    <img src="<?= $product->img_path ?>" alt="product image" style="width: 150px; height: 150px">
    <div>
        <h1><?= $product->title ?></h1>
        <p><?= $product->description ?></p>
        <p><?= $product->price ?></p>
    </div>
</div>
<?php endforeach; ?>
</body>
</html>