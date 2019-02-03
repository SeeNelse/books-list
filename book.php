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


  <?php

      require('scripts/db.php');
      $thisBookID = $_GET['id'];

      

      try {
        $connectDB = new PDO("mysql:host=$serverPath;dbname=$nameDB", $userDB, $passDB);
        $sqlRequestDB = $connectDB->prepare("SELECT * FROM catalog WHERE book_id=$thisBookID");
        $sqlRequestDB->execute();
      } catch (PDOExeption $e) {
        echo $sqlRequestDB.$e->getMessage();
      }


  ?>
  <div class="header">
  <h1 class="header__logo"><a class="header__logo-link" href="index.php">Книжный магазин</a></h1>
    <a href="/admin/index.php" class="header__admin-link">Админка</a>
  </div>
  <div class="wrapper">
    <?php
      forEach ($sqlRequestDB as $DBItem) {
    ?>
      <div class="book book-single" id="<?= $DBItem['book_id'] ?>">
        <h3 class="book__name"><?= $DBItem['book_name'] ?></h3>
        <p class="book__descr"><?= $DBItem['book_descr'] ?></p>
        <div class="book__bot">
          <span class="book__author"><?= $DBItem['book_author'] ?></span>
          <span class="book__genre"><?= $DBItem['book_genre'] ?></span>
        </div>
        <span class="book__price"><?= $DBItem['book_price'] ?></span>
        
        <form action="scripts/buy.php" method="POST" class="buy-book">
          <input type='hidden' name='id' value='<?= $DBItem['book_id'] ?>' class="buy-book__id" />
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
          <input class="buy-book__price" name="buy-all-price" data-price="<?= $DBItem['book_price'] ?>" value="<?= $DBItem['book_price'] ?>" type="text" readonly>
          </label>
          <button type="submit" class="buy-book__submit">Купить</button>
        </form>
      </div>
    <?php
      }
    ?>
  </div>

  <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
  <script src="js/script.js"></script>
</body>

</html>