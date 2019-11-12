function validateLemma(lemma) {
    if (lemma.length === 0) {
        return { error: 'Это поле обязательно для заполнения' };
    }
    var lang_id=$('#lang_id').val();
    var pattern = /^[a-zäöüčšž’\|\-\?\s\,\;\(\)]+$/i;
    if (lang_id == 3) { // English
        pattern = /^[a-z\|\-\?\s\,\;\(\)]+$/i;
    } else if (lang_id == 2) { // Russian
        pattern = /^[а-я\|\-\?\s\,\;\(\)]+$/i;
    }
    if (lemma.search(pattern) === -1) {
        return { error: 'В поле содержатся недопустимые символы' };
    }
    return { valid: true };
}

// $input - jQuery объект поля ввода input
// validate - функция валидации
function validateInput ($input, validate) {
  var result = validate($input.val());
  if (result.error) {
      $input.parent()
            .addClass('has-error')
            .children('.help-block')
            .text(result.error);                         
  } else {
      $input.parent()
            .removeClass('has-error')
            .children('.help-block')
            .text('');                         
  }
  return result.valid;
}
function checkLemmaForm() {
    var $signinLemma = $('#lemma').on('change keyup', function () {
        validateInput($signinLemma, validateLemma);
    });
    
    $('form').on('submit', function (e) {
        var valid = validateInput($signinLemma, validateLemma);
            /*& validateInput($signinPassword, validatePassword);*/
        if (!valid) {
/*                    alert('Error');            */
          e.preventDefault(); // если одно из полей не валидно, не отправляем форму
        }
    });
}