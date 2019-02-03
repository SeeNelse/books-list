<?php

  try {
    require('db.php');

    $thisBook = $_POST['id'];

    $formName = $_POST['edit-name'];
    $formDescr = $_POST['edit-descr'];
    $formAuthor = $_POST['edit-author'];
    $formGenre = $_POST['edit-genre'];
    $formPrice = $_POST['edit-price'];

    $connectDB = new PDO("mysql:host=$serverPath;dbname=$nameDB", $userDB, $passDB);

    // echo 'id' . $thisBook . $formName . $formDescr . $formAuthor . $formGenre . $formPrice;

    if (isset($thisBook)!='') {
      $sqlSend = "UPDATE catalog 
                  SET book_name='$formName', book_descr='$formDescr', book_author='$formAuthor', book_genre='$formGenre', book_price='$formPrice'
                  WHERE book_id=$thisBook";
      $connectDB -> exec($sqlSend);
    }

  header("Location: ../../admin.php");

  } catch (PDOExeption $e) {
    echo $sqlSend.$e->getMessage();
  }

?>