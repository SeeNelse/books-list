<?php

  require_once('config.php');
  require_once('functions.php');

  $addBook = new AddAndEditBooks($serverPath, $userDB, $passDB, $nameDB);
  $addBook->addBook($formName, $formDescr, $formAuthor, $formGenre, $formPrice);
  // header("Location: ../admin/index.php");

?>