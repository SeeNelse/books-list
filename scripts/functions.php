<?php

  // подключение/создание дб
  function CreateOrConnectToDB($serverPath, $userDB, $passDB, $nameDB) {
    try {
      $connectDB = new PDO("mysql:host=$serverPath", $userDB, $passDB);
      $sqlNewDB = "CREATE DATABASE IF NOT EXISTS $nameDB";
      $connectDB -> exec($sqlNewDB);
      $connectDB = null;
    } catch (PDOExeption $e) {
      return $sqlNewDB.$e->getMessage();
    }
  }
  
  // коннект к дб
  function connectToDB() {
    global $serverPath, $nameDB, $userDB, $passDB;
    return $connectDB = new PDO("mysql:host=$serverPath;dbname=$nameDB;charset=utf8", $userDB, $passDB);
  }
  // создание/подключение к нужным таблицам
  function CreateOrConnectToTable() {
    try {
      $connectDB = connectToDB();
      $connectDB->setAttribute(PDO:: ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);
      $sqlNewTable = "CREATE TABLE IF NOT EXISTS book (
                        book_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                        book_name CHAR(200) NOT NULL,
                        book_descr TEXT NOT NULL,
                        book_price INT(10) NOT NULL
                      ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

                      CREATE TABLE IF NOT EXISTS author (
                        author_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                        author_name CHAR(200) NOT NULL
                      ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

                      CREATE TABLE IF NOT EXISTS genre (
                        genre_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                        genre_name CHAR(200) NOT NULL
                      ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

                      CREATE TABLE IF NOT EXISTS book_author (
                        book_id_conn INT(10) NOT NULL,
                        author_id_conn INT(10) NOT NULL,
                        FOREIGN KEY (author_id_conn) REFERENCES author (author_id) ON DELETE CASCADE ON UPDATE CASCADE,
                        FOREIGN KEY (book_id_conn) REFERENCES book (book_id) ON DELETE CASCADE ON UPDATE CASCADE,
                        PRIMARY KEY (book_id_conn, author_id_conn) 
                      );
                      ALTER TABLE book_author ADD INDEX(book_id_conn, author_id_conn);
                      
                      CREATE TABLE IF NOT EXISTS book_genre (
                        book_id_conn INT(10) NOT NULL,
                        genre_id_conn INT(10) NOT NULL,
                        FOREIGN KEY (genre_id_conn) REFERENCES genre (genre_id) ON DELETE CASCADE ON UPDATE CASCADE,
                        FOREIGN KEY (book_id_conn) REFERENCES book (book_id) ON DELETE CASCADE ON UPDATE CASCADE,
                        PRIMARY KEY (book_id_conn, genre_id_conn) 
                      );
                      ALTER TABLE book_genre ADD INDEX(book_id_conn, genre_id_conn);";    
      $connectDB -> exec($sqlNewTable);
      return $connectDB;
    } catch (PDOExeption $e) {
      echo $sqlNewTable.$e->getMessage();
    }
  }

  // функция добавления новой книги
  function addBook($name, $descr, $author, $genre, $price) {
    $authorId = attrBookToArrayAndDisbandArray($author, 'author', true, null, null);
    $genreId = attrBookToArrayAndDisbandArray($genre, 'genre', true, null, null);
    try {
      $connectDB = connectToDB();
      if(isset($name)&&isset($descr)&&isset($price)&&isset($author)&&isset($genre)!="") {
        $sqlSendData .= "INSERT INTO book (book_name, book_descr, book_price) 
                    VALUES ('$name', '$descr', '$price');";
        $sqlTemp = $connectDB->prepare($sqlSendData);
        $sqlTemp->execute();
        $lastBookId = $connectDB->lastInsertId('book');

        $sqlSendtableRelation .= attrBookToArrayAndDisbandArray($authorId, 'author', false, $lastBookId, null);
        $sqlSendtableRelation .= attrBookToArrayAndDisbandArray($genreId, 'genre', false, $lastBookId, null);
        $sqlTempRelation = $connectDB->prepare($sqlSendtableRelation);
        $sqlTempRelation->execute();
      }
      $connectDB = null;
    } catch (PDOExeption $e) {
      echo $sqlSendData.$e->getMessage();
    }
  }

  // редактирование книги
  function editBook($formName, $formDescr, $formAuthor, $formGenre, $formPrice, $thisBook) {
    $authorId = attrBookToArrayAndDisbandArray($formAuthor, 'author', true, null, null);
    $genreId = attrBookToArrayAndDisbandArray($formGenre, 'genre', true, null, null);
    try {
      $connectDB = connectToDB();
      $sqlSendData .= "UPDATE book 
                      SET book_name='$formName', book_descr='$formDescr', book_price='$formPrice'
                      WHERE book_id=$thisBook";
      $sqlTemp = $connectDB->prepare($sqlSendData);
      $sqlTemp->execute();

      $sqlSendtableRelation .= attrBookToArrayAndDisbandArray($authorId, 'author', false, $thisBook, true);
      $sqlSendtableRelation .= attrBookToArrayAndDisbandArray($genreId, 'genre', false, $thisBook, true);
      $sqlTempRelation = $connectDB->prepare($sqlSendtableRelation);
      $sqlTempRelation->execute();

    } catch (PDOExeption $e) {
      echo $sqlSendData.$e->getMessage();
    }
  }

  // разбить полученные данные об авторе/жанре на массив
  function attrBookToArrayAndDisbandArray($array, $table, $getID, $bookID, $edit) {
    $array = explode(",", $array);
    $array = array_filter(array_map('trim', $array));
    if ($edit) {
      $sqlGetRelation .= $sqlSendDataBuferTable .= "DELETE FROM book_$table WHERE book_id_conn = $bookID;";
    }
    forEach ($array as $item) {
      if ($getID) {
        $sqlSendData .= getID($item, $table);
      } else {
        $sqlGetRelation .= tableRelation($item, $table, $bookID);
      }
    }
    return $getID ? $sqlSendData : $sqlGetRelation;
  }

  // вытягиваем id авторов/жанров
  function getID($item, $table) {
    try {
      $connectDB = connectToDB();
      $sqlGet = "SELECT ".$table.'_id'." FROM $table WHERE ".$table.'_name'."='$item'";
      $sqlArray = $connectDB -> query($sqlGet);
      $sqlArray = $sqlArray->fetchAll(PDO::FETCH_ASSOC);
      if(!$sqlArray) {
        $sqlSendData .= "INSERT INTO $table (".$table. '_name' . ") VALUES ('$item');";
        $sqlTemp = $connectDB->prepare($sqlSendData);
        $sqlTemp->execute();
        $lastID = $connectDB->lastInsertId($table);
        $connectDB = null;
        return $lastID . ',';
      } else {
        $connectDB = null;
        return (int)$sqlArray[0][$table.'_id'] . ',';
      }
    } catch (PDOExeption $e) {
      echo $sqlSendData.$e->getMessage();
    }
  }

  // записываем в таблицу связей
  function tableRelation($item, $table, $bookID) {
    $sqlSendDataBuferTable .= "INSERT INTO book_$table (book_id_conn, ".$table."_id_conn) VALUES ($bookID, $item);";
    return $sqlSendDataBuferTable;
  }

  // удаление книги
  function deleteBook($book) {
    try {
      $connectDB = connectToDB();
      if (isset($book)!='') {
        $sqlSend = "DELETE FROM book WHERE book_id=$book";
        $connectDB -> exec($sqlSend);
      }
  
    } catch (PDOExeption $e) {
      echo $sqlSend.$e->getMessage();
    }
  }

  // скрипт покупки книги
  function buyBook($thisBook, $name, $adress, $quantity, $allPrice) {
    try {
      $connectDB = connectToDB();
      $sqlSend = "SELECT * FROM book WHERE book_id=".$thisBook."";
      $state = $connectDB->query($sqlSend);
      $sqlTemp = $state->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOExeption $e) {
      echo $sqlTemp.$e->getMessage();
    }
  
  
    if(isset($name)&&isset($adress)&&isset($quantity)!="") { //Проверка отправилось ли наше поля name и не пустые ли они
      $to = $emailTo;
      $subject = 'Заказ книги';
      $message = "
        <html>
          <head>
            <title>$subject</title>
          </head>
          <body>
            <h4>Информация о заказе:</h4>
            <p>Название книги: ".$sqlTemp[0]['book_name']."</p>
            <p>Описание: ".$sqlTemp[0]['book_descr']."</p>
            <p>Автор: ".renderAttrInBook($thisBook, "author")."</p>
            <p>Жанр: ".renderAttrInBook($thisBook, "genre")."</p>
            <p>Цена за одну: ".$sqlTemp[0]['book_price']."</p>
            <br>
            <h4>Информация о покупателе:</h4>
            <p>ФИО: $name</p>
            <p>Адрес: $adress</p>
            <p>Количество: $quantity.</p>
            <p>Итоговая цена: $allPrice</p>
          </body>
        </html>";
      $headers  = "Content-type: text/html; charset=utf-8 \r\n";
      $headers .= "Заказ книги \r\n";
      mail($to, $subject, $message, $headers);
      header("Location: ../book.php?id=$thisBook");
    }

    echo $message;
  }

  // для рендера книг
  function renderBooks($admin, $bookID) {
    try {
      $connectDB = connectToDB();
      if ($bookID) {
        $sqlSend = "SELECT * FROM book WHERE book_id=$bookID;";
      }  else {
        $sqlSend = "SELECT * FROM book;";
      }
      $state = $connectDB->query($sqlSend);
      $sqlTemp = $state->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOExeption $e) {
      echo $sqlTemp.$e->getMessage();
      return;
    }

    forEach ($sqlTemp as $item) {
      ?>
      <div class="book" id="<?= $item['book_id'] ?>">
        <div class="book__wrapper">
          <h3 class="book__name"><?= $item['book_name'] ?></h3>
          <p class="book__descr"><?= $item['book_descr'] ?></p>
          <div class="book__bot">
            <span class="book__author"><?= renderAttrInBook($item['book_id'], "author") ?></span>
            <span class="book__genre"><?= renderAttrInBook($item['book_id'], "genre") ?></span>
          </div>
          <span class="book__price"><?= $item['book_price'] ?></span>
          <?php
            if(!$admin) {
              ?>
              <a href="book.php?id=<?= $item['book_id'] ?>" class="book__link">Купить</a>
              <?php
            }
          ?>
          <?php
            if ($admin) {
              ?>
              <div class="book__admin">
                <button class="book__edit">Редактировать</button>
                <a href="../scripts/deletebook.php/?id=<?= $item['book_id'] ?>" class="book__delete">Удалить</a>
              </div>
              <?php
            }
          ?>
        </div>
      </div>
      <?php
    }

    $connectDB = null;
  }

  // для рендера нужных авторов/жанров
  function renderAttrInBook($id, $attr) {
    try {
      $connectDB = connectToDB();
      $sqlSend = "SELECT book_id, ".$attr."_name
                  FROM
                      book_$attr
                  LEFT JOIN book ON book.book_id = book_$attr.book_id_conn
                  LEFT JOIN $attr ON $attr.".$attr."_id = book_$attr.".$attr."_id_conn
                  WHERE book_id_conn = $id";
      $sqlTemp = $connectDB->query($sqlSend);
      $sqlTemp = $sqlTemp->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOExeption $e) {
      echo $sqlTemp.$e->getMessage();
      return;
    }

    for ($i = 0; $i < count($sqlTemp); $i++) {
      if ($i == (int)count($sqlTemp)-1) {
        $attrList .= $sqlTemp[$i][$attr."_name"] . "";
      } else {
        $attrList .= $sqlTemp[$i][$attr."_name"] . ", ";
      }
    }
    return $attrList;
  }

  // рендер ссылок
  function renderLinks($table) {
    try {
      $connectDB = connectToDB();
      $sqlSend = "SELECT * FROM $table";
      $state = $connectDB->query($sqlSend);
      $sqlTemp = $state->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOExeption $e) {
      echo $sqlTemp.$e->getMessage();
      return;
    }

    forEach($sqlTemp as $item) {
    ?>
      <a href="index.php?<?=$table?>=<?=$item[$table.'_id']?>" class="book-filter__link"><?=$item[$table.'_name']?></a>
    <?php
    }
  }

  function renderAttrPage($id, $attr) {
    try {
      $connectDB = connectToDB();
      $sqlSend = "SELECT book_id_conn FROM book_$attr WHERE ".$attr."_id_conn = $id";
      $state = $connectDB->query($sqlSend);
      $sqlTemp = $state->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOExeption $e) {
      echo $sqlTemp.$e->getMessage();
      return;
    }
    
    forEach($sqlTemp as $item) {
      renderBooks(false, (int)$item['book_id_conn']);
    }
  }
  
?>