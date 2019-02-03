<?php

  try {
    require('db.php');

    $connectDB = new PDO("mysql:host=$serverPath;dbname=$nameDB", $userDB, $passDB);

    $thisBook = $_GET['id'];

    if (isset($thisBook)!='') {
      $sqlSend = "DELETE FROM catalog WHERE book_id=$thisBook";
      $connectDB -> exec($sqlSend);
    }

    header("Location: ../../admin/index.php");

  } catch (PDOExeption $e) {
    echo $sqlSend.$e->getMessage();
  }

?>