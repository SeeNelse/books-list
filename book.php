<?php
  require_once('scripts/config.php');
  require_once('scripts/functions.php');
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
    <a href="/admin/index.php" class="header__admin-link">Админка</a>
  </div>
  <div class="wrapper book-page">
    <?php renderBooks(false, $thisBookGet, false); ?>
    
    <form action="scripts/buy.php" method="POST" class="buy-book">
      <input type='hidden' name='id' value='<?= $thisBookGet ?>' class="buy-book__id" />
      <h3>Заказать книгу:</h3>
      <label>ФИО:</label>
      <input class="buy-book__field" type="text" placeholder="Фамилия Имя Отчество" name="buy-name" required>
      <label>Адрес:</label>
      <input class="buy-book__field" type="text" placeholder="Адрес доставки" name="buy-adress" required>
      <label>Кол-во экземпляров:</label>
      <div class="buy-book-quantity__block">
        <a href="javasctipt:void(0);" class="buy-book-quantity__minus">-</a>
        <input class="buy-book__field buy-book-quantity__input" type="number" placeholder="Количество" name="buy-quantity" value="1" required readonly>
        <a href="javasctipt:void(0);" class="buy-book-quantity__plus">+</a>
      </div>
      <label class="buy-book__full-price">Конечная цена: 
      <input class="buy-book__price" name="buy-all-price" data-price="" value="" type="text" readonly>
      </label>
      <button type="submit" class="buy-book__submit">Купить</button>
    </form>
  </div>
  </div>

  <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
  <script src="js/script.js"></script>
</body>

</html>