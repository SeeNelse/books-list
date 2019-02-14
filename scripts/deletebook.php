<?php
  require_once('config.php');
  require_once('functions.php');

  $deleteBook = new AddAndEditBooks($serverPath, $userDB, $passDB, $nameDB);
  $deleteBook->deleteBook($thisBookGet);
  header("Location: ../../admin/index.php");

?>