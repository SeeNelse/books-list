<?php
  require_once('scripts/config.php');
  require_once('scripts/functions.php');

  $CreateOrConnectToDB = new CreateOrConnect($serverPath, $userDB, $passDB, $nameDB);
  $CreateOrConnectToDB->toDB();
  $CreateOrConnectToDB->toTable();

  $renderBooks = new Render($serverPath, $userDB, $passDB, $nameDB);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="/css/style.css">
  <title>books</title>
</head>

<body>

  <div class="header">
    <h1 class="header__logo"><a class="header__logo-link" href="index.php">Книжный магазин</a></h1>
    <a href="admin/index.php" class="header__admin-link">Админка</a>
  </div>

  <div class="wrapper">
    <div class="book-filter">
      <div class="book-filter__block">
        <h3>Авторы:</h3>
        <?= $renderBooks->renderLinks('author'); ?>
      </div>

      <div class="book-filter__block">
        <h3>Жанры:</h3>
        <?= $renderBooks->renderLinks('genre'); ?>
      </div>
    </div>
    <div class="book-list">
      <?php
        if($_GET) {
          $renderBooks->renderAttrPage($attrID, $attrName);
        } else {
          $renderBooks->renderBooks(false, false);
        }
      ?>
    </div>
  </div>

  <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
  <script src="js/script.js"></script>
</body>

</html>

