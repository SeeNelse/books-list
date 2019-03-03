<?php

trait RenderHtml {

  private function renderBooksHtml($arrTemp, $admin) {
    forEach ($arrTemp as $item) {
      ?>
      <div class="book" id="<?= $item['book_id'] ?>">
        <div class="book__wrapper">
          <h3 class="book__name"><?= $item['book_name'] ?></h3>
          <p class="book__descr"><?= $item['book_descr'] ?></p>
          <div class="book__bot">
            <span class="book__author"><?= $this->renderAttrInBook($item['book_id'], "author") ?></span>
            <span class="book__genre"><?= $this->renderAttrInBook($item['book_id'], "genre") ?></span>
          </div>
          <span class="book__price"><?= $item['book_price'] ?></span>
          <?php
            if(!$admin) {
              ?>
              <a href="book.php?id=<?= $item['book_id'] ?>" class="book__link">Купить</a>
              <?php
            }
          ?>
          <?php
            if ($admin) {
              ?>
              <div class="book__admin">
                <button class="book__edit">Редактировать</button>
                <a href="../scripts/deletebook.php/?id=<?= $item['book_id'] ?>" class="book__delete">Удалить</a>
              </div>
              <?php
            }
          ?>
        </div>
      </div>
      <?php
    }
  }

}


?>