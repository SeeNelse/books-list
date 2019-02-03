<?php

  try {
    require('db.php');

    $formName = addslashes($_POST['b-name']);
    $formDescr = addslashes($_POST['b-descr']);
    $formAuthor = addslashes($_POST['b-author']);
    $formGenre = addslashes($_POST['b-genre']);
    $formPrice = addslashes($_POST['b-price']);

    $connectDB = new PDO("mysql:host=$serverPath;dbname=$nameDB", $userDB, $passDB);

    if(isset($formName)&&isset($formDescr)&&isset($formAuthor)&&isset($formGenre)&&isset($formPrice)!="") {

      $sqlSend = "INSERT INTO catalog (book_name, book_descr, book_author, book_genre, book_price) 
                VALUES ('$formName', '$formDescr', '$formAuthor', '$formGenre', '$formPrice')";
    
      $connectDB -> exec($sqlSend);

    }

    header("Location: ../admin.php");

  } catch (PDOExeption $e) {
    echo $sqlSend.$e->getMessage();
  }
  
?>
