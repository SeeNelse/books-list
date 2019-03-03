<?php

  trait WorkWithDB {
    // подключение к бд
    private function connect() {
      global $connectType;
      if ($connectType === 'pdo') {
        return $connectDB = new PDO("mysql:host=$this->serverPath;dbname=$this->nameDB;charset=utf8", $this->userDB, $this->passDB);
      } else if ($connectType === 'mysqli') {
        return $connectDB = new mysqli($this->serverPath, $this->userDB, $this->passDB, $this->nameDB);
      }
    }

    // Отправка в бд с помощью PDO или mysqli
    private function sendToDB($request, $getArr, $lastIdTable) {
      global $connectType;
      if ($connectType === 'pdo') {
        try {
          $connectToDB = $this -> connect();
          $state = $connectToDB -> query($request);
          if ($getArr) { // Проверка, нужен массив или просто отправка данных
            $arrTemp = $state->fetchAll(PDO::FETCH_ASSOC);
            return $arrTemp;
          } else {
            if ($lastIdTable) {
              return $lastID = $connectToDB->lastInsertId($table);
            }
            return $state;
          }
        } catch (PDOExeption $e) {
          echo $request.$e->getMessage();
        }
      } else if ($connectType === 'mysqli') {
        $connectToDB = $this -> connect();
        $state = $connectToDB -> multi_query($request);
        $result = $connectToDB -> store_result();
        if ($getArr) { // Проверка, нужен массив или просто отправка данных
          while ($row = $result->fetch_assoc()) {
            $arrTemp[] = $row;
          }
          return $arrTemp;
        } else {
          if ($lastIdTable) {
            $lastID = $connectToDB->insert_id;
            return $lastID;
          }
          return $state;
        }
      } else {
        echo 'Ошибка в переменной connectType';
      }
    }
  }

  // Создание/коннект к базе и таблице
  class CreateOrConnect {

    private $serverPath;
    private $userDB;
    private $passDB;
    private $nameDB;

    use WorkWithDB;


    public function __construct($serverPath, $userDB, $passDB, $nameDB) {
      $this->serverPath = $serverPath;
      $this->userDB = $userDB;
      $this->passDB = $passDB;
      $this->nameDB = $nameDB;
    }

    public function toDB() {
      global $connectType;
      if ($connectType === 'pdo') {
        try {
          $connectDB = new PDO("mysql:host=$this->serverPath", $this->userDB, $this->passDB);
          $sqlNewDB = "CREATE DATABASE IF NOT EXISTS $this->nameDB";
          $connectDB -> query($sqlNewDB);
          $connectDB = null;
        } catch (PDOExeption $e) {
          return $sqlNewDB.$e->getMessage();
        }
      } else if ($connectType === 'mysqli') {
        $connectDB = new mysqli($this->serverPath, $this->userDB, $this->passDB);
        $sqlNewDB = "CREATE DATABASE IF NOT EXISTS $this->nameDB";
        $connectDB -> query($sqlNewDB);
        $connectDB = null;
      }
    }

    public function toTable() {
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
      $connectDB = $this->sendToDB($sqlNewTable, false, false);
      $connectDB = null;
    }

  }

  // Для рендера книг на главной, авторов/жанров, ссылок. Покупка книги
  class Render {

    private $serverPath;
    private $userDB;
    private $passDB;
    private $nameDB;

    use WorkWithDB;
    use RenderHtml;

    public function __construct($serverPath, $userDB, $passDB, $nameDB) {
      $this->serverPath = $serverPath;
      $this->userDB = $userDB;
      $this->passDB = $passDB;
      $this->nameDB = $nameDB;
    }

    // для рендера книг
    public function renderBooks($admin, $bookID) {
      if ($bookID) {
        $sqlSend = "SELECT * FROM book WHERE book_id=$bookID;";
      }  else {
        $sqlSend = "SELECT * FROM book;";
      }

      $arrTemp = $this->sendToDB($sqlSend, true, false);
      if (!$arrTemp) {
        return;
      }
      $this->renderBooksHtml($arrTemp, $admin);

      $connectDB = null;
    }

    // для рендера нужных авторов/жанров
    public function renderAttrInBook($id, $attr) {
      $sqlSend = "SELECT book_id, ".$attr."_name
                    FROM
                        book_$attr
                    LEFT JOIN book ON book.book_id = book_$attr.book_id_conn
                    LEFT JOIN $attr ON $attr.".$attr."_id = book_$attr.".$attr."_id_conn
                    WHERE book_id_conn = $id";
      $arrTemp = $this->sendToDB($sqlSend, true, false);

      for ($i = 0; $i < count($arrTemp); $i++) {
        if ($i == (int)count($arrTemp)-1) {
          $attrList .= $arrTemp[$i][$attr."_name"] . "";
        } else {
          $attrList .= $arrTemp[$i][$attr."_name"] . ", ";
        }
      }
      return $attrList;
    }

    // рендер ссылок
    public function renderLinks($table) {
      $sqlSend = "SELECT * FROM $table";
      $arrTemp = $this->sendToDB($sqlSend, true, false);

      if (!$arrTemp) {
        return;
      }
      forEach($arrTemp as $item) {
        ?>
          <a href="index.php?<?=$table?>=<?=$item[$table.'_id']?>" class="book-filter__link"><?=$item[$table.'_name']?></a>
        <?php
      }
    }

    // рендер страницы с нужными авторми/жанрами
    public function renderAttrPage($id, $attr) {
      $sqlSend = "SELECT book_id_conn FROM book_$attr WHERE ".$attr."_id_conn = $id";
      $arrTemp = $this->sendToDB($sqlSend, true, false);
      
      forEach($arrTemp as $item) {
        $this->renderBooks(false, (int)$item['book_id_conn']);
      }
    }

    // скрипт покупки книги
    public function buyBook($thisBook, $name, $adress, $quantity, $allPrice) {
      $sqlSend = "SELECT * FROM book WHERE book_id=".$thisBook."";
      $arrTemp = $this->sendToDB($sqlSend, true, false);
    
    
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
              <p>Название книги: ".$arrTemp[0]['book_name']."</p>
              <p>Описание: ".$arrTemp[0]['book_descr']."</p>
              <p>Автор: ".$this->renderAttrInBook($thisBook, "author")."</p>
              <p>Жанр: ".$this->renderAttrInBook($thisBook, "genre")."</p>
              <p>Цена за одну: ".$arrTemp[0]['book_price']."</p>
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

  // Для добавления, удаления и редактирования книг
  class AddAndEditBooks {

    private $serverPath;
    private $userDB;
    private $passDB;
    private $nameDB;

    use WorkWithDB;

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

      if(isset($name)&&isset($descr)&&isset($price)&&isset($author)&&isset($genre)!="") {
        $sqlSendData .= "INSERT INTO book (book_name, book_descr, book_price) 
                    VALUES ('$name', '$descr', '$price');";
        $lastBookId = $this->sendToDB($sqlSendData, false, 'book');

        $sqlSendtableRelation .= $this->attrBookToArrayAndDisbandArray($authorId, 'author', false, $lastBookId, null);
        $sqlSendtableRelation .= $this->attrBookToArrayAndDisbandArray($genreId, 'genre', false, $lastBookId, null);

        $sendToBD = $this->sendToDB($sqlSendtableRelation, false, false);
      }

    }

    // редактирование книги
    public function editBook($formName, $formDescr, $formAuthor, $formGenre, $formPrice, $thisBook) {
      $authorId = $this->attrBookToArrayAndDisbandArray($formAuthor, 'author', true, null, null);
      $genreId = $this->attrBookToArrayAndDisbandArray($formGenre, 'genre', true, null, null);

      $sqlSendData .= "UPDATE book 
                      SET book_name='$formName', book_descr='$formDescr', book_price='$formPrice'
                      WHERE book_id=$thisBook";
      $sqlTemp = $this->sendToDB($sqlSendData, false, false);

      $sqlSendtableRelation .= $this->attrBookToArrayAndDisbandArray($authorId, 'author', false, $thisBook, true);
      $sqlSendtableRelation .= $this->attrBookToArrayAndDisbandArray($genreId, 'genre', false, $thisBook, true);
      $sendToBD = $this->sendToDB($sqlSendtableRelation, false, false);
    }

    // вытягиваем id авторов/жанров
    public function getID($item, $table) {
      global $connectType;
      $sqlGet = "SELECT ".$table.'_id'." FROM $table WHERE ".$table.'_name'."='$item'";
      $sqlArray = $this->sendToDB($sqlGet, true, false);
      if(!$sqlArray) {
        $sqlSendData .= "INSERT INTO $table (".$table. '_name' . ") VALUES ('$item');";
        $lastID = $this->sendToDB($sqlSendData, false, $table);
        return $lastID . ',';
      } else {
        $connectDB = null;
        return (int)$sqlArray[0][$table.'_id'] . ',';
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

    // записываем в таблицу связей
    public function tableRelation($item, $table, $bookID) {
      $sqlSendDataBuferTable .= "INSERT INTO book_$table (book_id_conn, ".$table."_id_conn) VALUES ($bookID, $item); ";
      return $sqlSendDataBuferTable;
    }

    // удаление книги
    public function deleteBook($book) {
      if (isset($book)!='') {
        $sqlSend = "DELETE FROM book WHERE book_id=$book";
        $this->sendToDB($sqlSend, false, false);
      }
    }

  }

  
?>
