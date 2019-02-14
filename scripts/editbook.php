<?php
  require_once('config.php');
  require_once('functions.php');

  $editBook = new AddAndEditBooks($serverPath, $userDB, $passDB, $nameDB);
  $editBook->editBook($formName, $formDescr, $formAuthor, $formGenre, $formPrice, $thisBookPost);
  header("Location: ../admin/index.php");
  
?>