<?php
  require_once('config.php');
  require_once('functions.php');

  deleteBook($thisBookGet);
  header("Location: ../../admin/index.php");

?>