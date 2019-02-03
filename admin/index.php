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

      require('../scripts/db.php');

      try {
        $connectDB = new PDO("mysql:host=$serverPath;dbname=$nameDB", $userDB, $passDB);

        $sqlRequestDB = $connectDB->prepare("SELECT * FROM catalog");
        $sqlRequestDB->execute();
      } catch (PDOExeption $e) {
        echo $sqlRequestDB.$e->getMessage();
      }

  ?>
  <div class="header">
    <h1 class="header__logo"><a class="header__logo-link" href="../index.php">Книжный магазин</a></h1>
    <a href="#" class="header__admin-link">Выйти</a>
  </div>

  <div class="wrapper admin">
    <form action="../scripts/addbook.php" method="POST" class="add-book">
      <label>Имя книги:</label>
      <input class="add-book__field" type="text" placeholder="Введите имя книги" name="b-name" required>
      <label>Описание книги:</label>
      <textarea class="add-book__field" name="b-descr" id="" cols="30" rows="10" placeholder="Введите описание книги" required></textarea>
      <label>Автор(через запятую):</label>
      <input class="add-book__field" type="text" placeholder="Автор" name="b-author" required>
      <label>Жанр(через запятую):</label>
      <input class="add-book__field" type="text" placeholder="Жанр" name="b-genre" required>
      <label>Цена:</label>
      <input class="add-book__field" type="number" placeholder="Цена" name="b-price" required>
      <button type="submit" class="add-book__submit">Добавить</button>
    </form>

    <div class="book-list">
      <?php
        forEach ($sqlRequestDB as $DBItem) {
      ?>
        <div class="book" id="<?= $DBItem['book_id'] ?>">
          <div class="book__wrapper">
            <h3 class="book__name"><?= $DBItem['book_name'] ?></h3>
            <p class="book__descr_head">Описание:</p>
            <p class="book__descr"><?= $DBItem['book_descr'] ?></p>
            <div class="book__bot">
              <span class="book__author"><?= $DBItem['book_author'] ?></span>
              <span class="book__genre"><?= $DBItem['book_genre'] ?></span>
            </div>
            <span class="book__price"><?= $DBItem['book_price'] ?></span>
          </div> 
          <div class="book__admin">
            <button class="book__edit">Редактировать</button>
            <a href="../scripts/deletebook.php/?id=<?= $DBItem['book_id'] ?>" class="book__delete">Удалить</a>
          </div>
        </div>
      <?php
        }
      ?>
    </div>
  </div>

  <form action="../scripts/editbook.php" method="POST" id="edit-book" class="add-book edit-book">
    <input type='hidden' name='id' value='' class="edit-book__id" />
    <label>Имя книги:</label>
    <input class="add-book__field add-book__name" type="text" placeholder="Введите имя книги" name="edit-name" required>
    <label>Описание книги:</label>
    <textarea class="add-book__field add-book__descr" name="edit-descr" id="" cols="30" rows="10" placeholder="Введите описание книги" required></textarea>
    <label>Автор:</label>
    <input class="add-book__field add-book__author" type="text" placeholder="Автор" name="edit-author" required>
    <label>Жанр:</label>
    <input class="add-book__field add-book__genre" type="text" placeholder="Жанр" name="edit-genre" required>
    <label>Цена:</label>
    <input class="add-book__field add-book__price" type="number" placeholder="Цена" name="edit-price" required>
    <div class="edit-book__bot">
      <button type="submit" class="add-book__submit" name="some_name" id="123">Изменить</button>
      <span class="add-book__cancel">Отмена</span>
    </div>
  </form>

  <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
  <script src="../js/script.js"></script>
</body>

</html>