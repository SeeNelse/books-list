<?php 
  
  // данный бд
  $serverPath = 'localhost';
  $userDB = 'root';
  $passDB = '';
  $nameDB = 'booksDB';

  // куда отправляется заявка на покупку
  $emailTo = "seenelse@gmail.com";

  // данные с формы добавления новой книги(addBook.php) или редактирование(editbook.php)
  $formName = addslashes($_POST['b-name']);
  $formDescr = addslashes($_POST['b-descr']);
  $formAuthor = addslashes($_POST['b-author']);
  $formGenre = addslashes($_POST['b-genre']);
  $formPrice = addslashes($_POST['b-price']);

  $thisBookPost = $_POST['id'];
  $thisBookGet = $_GET['id'];

  $attrName = array_keys($_GET)[0];
  $attrID = (int)$_GET[$attrName];
?>