<?php

require_once 'config.php';
require_once 'common.php';

if (
    isset($_POST['username'])
    && isset($_POST['password'])
    && $_POST['username'] === USERNAME
    && $_POST['password'] === PASSWORD
) {
    $_SESSION['auth'] = true;
    redirect('products');
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
    <input type="text" name="username" id="username"> <br>
    <label for="password"><?= translate('Password :'); ?></label>
    <input type="password" name="password" id="password"> <br>
    <input type="submit" value="<?= translate('Login'); ?>">
</form>
</body>
</html>
