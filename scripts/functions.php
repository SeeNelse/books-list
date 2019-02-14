<?php

  trait ConnectToDB {
    private function connect() {
      return $connectDB = new PDO("mysql:host=$this->serverPath;dbname=$this->nameDB;charset=utf8", $this->userDB, $this->passDB);
    }
  }

  // Создание/коннект к базе и таблице
  class CreateOrConnect {

    private $serverPath;
    private $userDB;
    private $passDB;
    private $nameDB;

    use ConnectToDB;

    public function __construct($serverPath, $userDB, $passDB, $nameDB) {
      $this->serverPath = $serverPath;
      $this->userDB = $userDB;
      $this->passDB = $passDB;
      $this->nameDB = $nameDB;
    }

    public function toDB() {
      try {
        $connectDB = new PDO("mysql:host=$this->serverPath", $this->userDB, $this->passDB);
        $sqlNewDB = "CREATE DATABASE IF NOT EXISTS $this->nameDB";
        $connectDB -> exec($sqlNewDB);
        $connectDB = null;
      } catch (PDOExeption $e) {
        return $sqlNewDB.$e->getMessage();
      }
    }

    public function toTable() {
      try {
        $connectDB = $this->connect();
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

  }

  // Для рендера книг на главной, авторов/жанров, ссылок. Покупка книги
  class Render {

    private $serverPath;
    private $userDB;
    private $passDB;
    private $nameDB;

    use ConnectToDB;

    public function __construct($serverPath, $userDB, $passDB, $nameDB) {
      $this->serverPath = $serverPath;
      $this->userDB = $userDB;
      $this->passDB = $passDB;
      $this->nameDB = $nameDB;
    }

    // для рендера книг
    public function renderBooks($admin, $bookID) {
      try {
        $connectDB = $this->connect();
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
              <span class="book__author"><?= $this->renderAttrInBook($item['book_id'], "author") ?></span>
              <span class="book__genre"><?= $this->renderAttrInBook($item['book_id'], "genre") ?></span>
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
    public function renderAttrInBook($id, $attr) {
      try {
        $connectDB = $this->connect();
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
    public function renderLinks($table) {
      try {
        $connectDB = $this->connect();
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

    // рендер страницы с нужными авторми/жанрами
    public function renderAttrPage($id, $attr) {
      try {
        $connectDB = $this->connect();
        $sqlSend = "SELECT book_id_conn FROM book_$attr WHERE ".$attr."_id_conn = $id";
        $state = $connectDB->query($sqlSend);
        $sqlTemp = $state->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOExeption $e) {
        echo $sqlTemp.$e->getMessage();
        return;
      }
      
      forEach($sqlTemp as $item) {
        $this->renderBooks(false, (int)$item['book_id_conn']);
      }
    }

    // скрипт покупки книги
    public function buyBook($thisBook, $name, $adress, $quantity, $allPrice) {
      try {
        $connectDB = $this->connect();
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
              <p>Автор: ".$this->renderAttrInBook($thisBook, "author")."</p>
              <p>Жанр: ".$this->renderAttrInBook($thisBook, "genre")."</p>
              <p>Цена за одну: ".$sqlTemp[0]['book_price']."</p>
              <br>
              <h4>Информация о покупателе:</h4>
              <p>ФИО: $name</p>
              <p>Адрес: $adress</p>
              <p>Количество: $quantity</p>
              <p>Итоговая цена: $allPrice</p>
            </body>
          </html>";
        $headers  = "Content-type: text/html; charset=utf-8 \r\n";
        $headers .= "Заказ книги \r\n";
        mail($to, $subject, $message, $headers);
        header("Location: ../book.php?id=$thisBook");
      }

      // echo $message;
    }

  }

  class AddAndEditBooks {

    private $serverPath;
    private $userDB;
    private $passDB;
    private $nameDB;

    use ConnectToDB;

    public function __construct($serverPath, $userDB, $passDB, $nameDB) {
      $this->serverPath = $serverPath;
      $this->userDB = $userDB;
      $this->passDB = $passDB;
      $this->nameDB = $nameDB;
    }

    // функция добавления новой книги
    public function addBook($name, $descr, $author, $genre, $price) {
      $authorId = $this->attrBookToArrayAndDisbandArray($author, 'author', true, null, null);
      $genreId = $this->attrBookToArrayAndDisbandArray($genre, 'genre', true, null, null);
      try {
        $connectDB = $this->connect();
        if(isset($name)&&isset($descr)&&isset($price)&&isset($author)&&isset($genre)!="") {
          $sqlSendData .= "INSERT INTO book (book_name, book_descr, book_price) 
                      VALUES ('$name', '$descr', '$price');";
          $sqlTemp = $connectDB->prepare($sqlSendData);
          $sqlTemp->execute();
          $lastBookId = $connectDB->lastInsertId('book');

          $sqlSendtableRelation .= $this->attrBookToArrayAndDisbandArray($authorId, 'author', false, $lastBookId, null);
          $sqlSendtableRelation .= $this->attrBookToArrayAndDisbandArray($genreId, 'genre', false, $lastBookId, null);
          $sqlTempRelation = $connectDB->prepare($sqlSendtableRelation);
          $sqlTempRelation->execute();
        }
        $connectDB = null;
      } catch (PDOExeption $e) {
        echo $sqlSendData.$e->getMessage();
      }
    }

    // редактирование книги
    public function editBook($formName, $formDescr, $formAuthor, $formGenre, $formPrice, $thisBook) {
      $authorId = $this->attrBookToArrayAndDisbandArray($formAuthor, 'author', true, null, null);
      $genreId = $this->attrBookToArrayAndDisbandArray($formGenre, 'genre', true, null, null);
      try {
        $connectDB = $this->connect();
        $sqlSendData .= "UPDATE book 
                        SET book_name='$formName', book_descr='$formDescr', book_price='$formPrice'
                        WHERE book_id=$thisBook";
        $sqlTemp = $connectDB->prepare($sqlSendData);
        $sqlTemp->execute();

        $sqlSendtableRelation .= $this->attrBookToArrayAndDisbandArray($authorId, 'author', false, $thisBook, true);
        $sqlSendtableRelation .= $this->attrBookToArrayAndDisbandArray($genreId, 'genre', false, $thisBook, true);
        $sqlTempRelation = $connectDB->prepare($sqlSendtableRelation);
        $sqlTempRelation->execute();

      } catch (PDOExeption $e) {
        echo $sqlSendData.$e->getMessage();
      }
    }

    // разбить полученные данные об авторе/жанре на массив
    public function attrBookToArrayAndDisbandArray($array, $table, $getID, $bookID, $edit) {
      $array = explode(",", $array);
      $array = array_filter(array_map('trim', $array));
      if ($edit) {
        $sqlGetRelation .= $sqlSendDataBuferTable .= "DELETE FROM book_$table WHERE book_id_conn = $bookID;";
      }
      forEach ($array as $item) {
        if ($getID) {
          $sqlSendData .= $this->getID($item, $table);
        } else {
          $sqlGetRelation .= $this->tableRelation($item, $table, $bookID);
        }
      }
      return $getID ? $sqlSendData : $sqlGetRelation;
    }

    // вытягиваем id авторов/жанров
    public function getID($item, $table) {
      try {
        $connectDB = $this->connect();
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
    public function tableRelation($item, $table, $bookID) {
      $sqlSendDataBuferTable .= "INSERT INTO book_$table (book_id_conn, ".$table."_id_conn) VALUES ($bookID, $item);";
      return $sqlSendDataBuferTable;
    }

    // удаление книги
    public function deleteBook($book) {
      try {
        $connectDB = $this->connect();
        if (isset($book)!='') {
          $sqlSend = "DELETE FROM book WHERE book_id=$book";
          $connectDB -> exec($sqlSend);
        }
    
      } catch (PDOExeption $e) {
        echo $sqlSend.$e->getMessage();
      }
    }

  }

  
  
?>