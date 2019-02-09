<?php

  require_once('config.php');
  require_once('functions.php');


  addBook($formName, $formDescr, $formAuthor, $formGenre, $formPrice);
  header("Location: ../admin/index.php");

?>