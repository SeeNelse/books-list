<?
  require('db.php');

  $thisBook = $_POST['id'];

  $connectDB = new PDO("mysql:host=$serverPath;dbname=$nameDB", $userDB, $passDB);

  $sqlRequestDB = $connectDB->prepare("SELECT * FROM catalog WHERE book_id=$thisBook");
  $sqlRequestDB->execute();

  forEach ($sqlRequestDB as $DBItem) {
    $bookName = $DBItem['book_name'];
    $bookDescr =  $DBItem['book_descr'];
    $bookAuthor =  $DBItem['book_author'];
    $bookGenre =  $DBItem['book_genre'];
    $bookPrice =  $DBItem['book_price'];
  }

  if(isset($_POST['buy-name'])&&isset($_POST['buy-adress'])&&isset($_POST['buy-quantity'])!="") { //Проверка отправилось ли наше поля name и не пустые ли они
    $to = 'seenelse@gmail.com';
    $subject = 'Заказ книги'; //Загаловок сообщения
    $message = '
      <html>
        <head>
          <title>'.$subject.'</title>
        </head>
        <body>
          <h4>Информация о заказе:</h4>
          <p>Название книги: '.$bookName.'</p>
          <p>Описание: '.$bookDescr.'</p>
          <p>Автор: '.$bookAuthor.'</p>
          <p>Жанр: '.$bookGenre.'</p>
          <p>Цена за одну: '.$bookPrice.'</p>
          <br>
          <h4>Информация о покупателе:</h4>
          <p>ФИО: '.$_POST['buy-name'].'</p>
          <p>Адрес: '.$_POST['buy-adress'].'</p>
          <p>Количество: '.$_POST['buy-quantity'].'</p>
          <p>Итоговая цена: '.$_POST['buy-all-price'].'</p>
        </body>
      </html>';
    $headers  = "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "Заказ книги \r\n";
    mail($to, $subject, $message, $headers);
    header("Location: ../book.php?id=$thisBook");
  }
?>