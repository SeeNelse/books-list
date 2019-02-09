<?
  require_once('config.php');
  require_once('functions.php');

  buyBook($thisBookPost, $_POST['buy-name'], $_POST['buy-adress'], $_POST['buy-quantity'], $_POST['buy-all-price']);

?>