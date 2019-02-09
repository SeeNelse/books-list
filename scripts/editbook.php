<?php
  require_once('config.php');
  require_once('functions.php');

  editBook($formName, $formDescr, $formAuthor, $formGenre, $formPrice, $thisBookPost);
  header("Location: ../admin/index.php");
  
?>