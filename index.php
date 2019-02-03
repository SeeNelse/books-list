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

      // подключение/создание дб
      try {
        $connectDB = new PDO("mysql:host=$serverPath", $userDB, $passDB);
        $sqlNewDB = "CREATE DATABASE IF NOT EXISTS $nameDB";
        $connectDB -> exec($sqlNewDB);
      } catch (PDOExeption $e) {
        echo $sqlNewDB.$e->getMessage();
      }

      // подключение/создание таблицы и столбцов в дб
      try {
        $connectDB = new PDO("mysql:host=$serverPath;dbname=$nameDB", $userDB, $passDB);
        $connectDB->setAttribute(PDO:: ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);
        $sqlNewTable = "CREATE TABLE IF NOT EXISTS catalog (
          book_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
          book_name CHAR(100) NOT NULL,
          book_descr VARCHAR(2000) NOT NULL,
          book_author CHAR(200) NOT NULL,
          book_genre CHAR(100) NOT NULL,
          book_price INT(30) NOT NULL
              ) ";
        $connectDB -> exec($sqlNewTable);
      } catch (PDOExeption $e) {
        echo $sqlNewTable.$e->getMessage();
      }

      // подключение к нужным каталогам
      try {
        $sqlRequestDB = $connectDB->prepare("SELECT * FROM catalog");
        $sqlRequestDB->execute();
      } catch (PDOExeption $e) {
        echo $sqlRequestDB.$e->getMessage();
      }
      try {
        $filterBooksAuthor = $connectDB->prepare("SELECT * FROM catalog");
        $filterBooksAuthor->execute();
      } catch (PDOExeption $e) {
        echo $filterBooksAuthor.$e->getMessage();
      }
      try {
        $filterBooksGenre = $connectDB->prepare("SELECT * FROM catalog");
        $filterBooksGenre->execute();
      } catch (PDOExeption $e) {
        echo $filterBooksGenre.$e->getMessage();
      }

  ?>
  <div class="header">
  <h1 class="header__logo"><a class="header__logo-link" href="index.php">Книжный магазин</a></h1>
    <a href="/admin/index.php" class="header__admin-link">Админка</a>
  </div>

  <div class="wrapper">
    <div class="book-filter">
      <div class="book-filter__block">
        <h3>Авторы:</h3>
        <?php 
          // рендер ссылок на авторов
          forEach ($filterBooksAuthor as $filterItem) {
            $filterAuthor = explode(",", $filterItem['book_author']);
            forEach ($filterAuthor as $filterAuthorItem) {
              $filterAuthorAll[] = trim($filterAuthorItem);
            }
          }
          if ($filterAuthorAll) {
            $filterAuthorAll = array_unique($filterAuthorAll);
            forEach ($filterAuthorAll as $fiterAuthorItem) {
              ?>
                <a href="archive.php?author=<?= $fiterAuthorItem ?>" class="book-filter__link"><?= $fiterAuthorItem ?></a>
              <?php
            }
          } else {
            ?>
              <span class="book-filter__current">Нет авторов</span>
            <?php
          }
        ?>
      </div>

      <div class="book-filter__block">
        <h3>Жанры:</h3>
        <?php 
          // рендер ссылок на жанры
          forEach ($filterBooksGenre as $filterItem) {
            $filterGenre = explode(",", $filterItem['book_genre']);
            forEach ($filterGenre as $filterGenreItem) {
              $filterGenreAll[] = trim($filterGenreItem);
            }
          }
          if ($filterGenreAll) {
            $filterGenreAll = array_unique($filterGenreAll);
            forEach ($filterGenreAll as $fiterAuthorItem) {
              ?>
                <a href="archive.php?genre=<?= $fiterAuthorItem ?>" class="book-filter__link"><?= $fiterAuthorItem ?></a>
              <?php
            }
          } else {
            ?>
              <span class="book-filter__current">Нет жанров</span>
            <?php
          }
        ?>
      </div>
    </div>
    <div class="book-list">
      <?php
        forEach ($sqlRequestDB as $DBItem) {
      ?>
        <div class="book" <?= $DBItem['book_id'] ?>>
          <h3 class="book__name"><?= $DBItem['book_name'] ?></h3>
          <p class="book__descr"><?= $DBItem['book_descr'] ?></p>
          <div class="book__bot">
            <span class="book__author"><?= $DBItem['book_author'] ?></span>
            <span class="book__genre"><?= $DBItem['book_genre'] ?></span>
          </div>
          <span class="book__price"><?= $DBItem['book_price'] ?></span>
          <a href="book.php?id=<?= $DBItem['book_id'] ?>" class="book__link">Купить</a>
        </div>
        
      <?php
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

