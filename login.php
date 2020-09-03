<?php

require_once 'config.php';
require_once 'common.php';

$errors = [];

if (isset($_POST['submit'])) {
    if (!isset($_POST['username']) || !$_POST['username']) {
        $errors['username'] = 'Username is required';
    }

    if (!isset($_POST['username']) || !$_POST['password']) {
        $errors['password'] = 'Password is required';
    }

    if (
        !$errors
        && $_POST['username'] === USERNAME
        && $_POST['password'] === PASSWORD
    ) {
        $_SESSION['auth'] = true;
        redirect('products');
    }

    $errors['form'] = 'Username or password is invalid.';
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
<form action="login.php" method="post">

    <label for="username"><?= translate('Username :'); ?></label>
    <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? '' ?>"> <br>
    <?php
    if (isset($errors['username'])): ?>
        <p style="color: red"> <?= $errors['username'] ?> </p> <br>
    <?php
    endif; ?>

    <label for="password"><?= translate('Password :'); ?></label>
    <input type="password" name="password" id="password"> <br>
    <?php
    if (isset($errors['password'])): ?>
        <p style="color: red"> <?= $errors['password'] ?> </p> <br>
    <?php
    endif; ?>

    <input type="submit" name="submit" value="<?= translate('Login'); ?>">
    <?php
    if (isset($errors['form'])): ?>
        <p style="color: red"> <?= $errors['form'] ?> </p> <br>
    <?php
    endif; ?>

</form>


</body>
</html>
