$(document).ready(function() {

  // редактирование книг
  $('.book__edit').click(function() {
    $(this).parents('.book__wrapper').hide()

    $('.edit-book').append().css('display', 'flex').appendTo($(this).parents('.book'));
    $('.edit-book .add-book__name').val($(this).parents('.book').find('.book__name').html());
    $('.edit-book .add-book__descr').val($(this).parents('.book').find('.book__descr').html());
    $('.edit-book .add-book__author').val($(this).parents('.book').find('.book__author').html());
    $('.edit-book .add-book__genre').val($(this).parents('.book').find('.book__genre').html());
    $('.edit-book .add-book__price').val($(this).parents('.book').find('.book__price').html());
    $('.edit-book .edit-book__id').val(+$(this).parents('.book').attr('id'));
  });

  $('.add-book__cancel').click(function() {
    $(this).parents('.book').find('.book__wrapper').show();
    $(this).parents('.edit-book').hide();
  });


  $('.buy-book__price').attr('data-price', $('.book__price').html());
  $('.buy-book__price').val($('.book__price').html());
  // кол-во книг при покупке
  $('.buy-book-quantity__plus').click(function() {
    let inputValue = +$('.buy-book-quantity__input').val();
    $('.buy-book-quantity__input').val(inputValue+1);
    PriceForAll(inputValue+1);
  });

  $('.buy-book-quantity__minus').click(function() {
    let inputValue = +$('.buy-book-quantity__input').val();
    if (inputValue === 1) {
      return;
    }
    $('.buy-book-quantity__input').val(inputValue-1);
    PriceForAll(inputValue-1);
  });
  function PriceForAll(quant) {
    $('.buy-book__price').val(+$('.buy-book__price').attr('data-price') * quant);
  }

});