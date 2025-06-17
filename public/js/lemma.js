function validateLemma(lemma) {
    if (lemma.length === 0) {
        return { error: 'Это поле обязательно для заполнения' };
    }
    var lang_id=$('#lang_id').val();
    var pattern = /^[a-zäöüčšž’\|\-\?\s\,\;\(\)\}\{\:\.\/\[\]]+$/i;
    if (lang_id == 3) { // English
        pattern = /^[a-z\|\-\?\s\,\;\(\)\.]+$/i;
    } else if (lang_id == 2) { // Russian
        pattern = /^[а-я\|\-\?\s\,\;\(\)\.]+$/i;
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

function reloadStemAffixByWordforms(el, locale) {
    var id = $(el).data('reload');
    
    $("#lemmaStemAffix").empty();
    $("#img-loading_stem-affix").show();
    $.ajax({
        url: '/' + locale + '/dict/lemma/'+ id + '/reload_stem_affix_by_wordforms', 
        type: 'GET',
        success: function(result){
            $("#lemmaStemAffix").html(result);
            $("#img-loading_stem-affix").hide();                
        },
        error: function() {
            $("#lemmaStemAffix").html('ERROR'); 
            $("#img-loading_stem-affix").hide();                
        }
    }); 
}

function setStatus(id, label_id) {
    var new_status = $("#status-"+id).attr("data-new");
    var old_status = $("#status-"+id).attr("data-old");
//console.log(new_status, old_status);    
    $.ajax({
        url: '/dict/lemma/'+ id + '/' + label_id + '/set_status/' + new_status, 
        type: 'GET',
        success: function(status){
            $("#status-"+id).removeClass('status'+old_status);
            $("#status-"+id).addClass('status'+status);
            $("#status-"+id).attr("data-new", old_status);
            $("#status-"+id).attr("data-old", status);
//console.log($("#status-"+id).attr("data-new"), $("#status-"+id).attr("data-old"));    
        },
        error: function() {
        }
    }); 
}   

function removeLabel(lemma_id, label_id) {
    $.ajax({
        url: '/dict/lemma/'+ lemma_id + '/' + label_id + '/remove_label', 
        type: 'GET',
        success: function(){
            $("#row-"+lemma_id).hide();
        },
    }); 
}

function addLabel(lemma_id, label_id) {
    $.ajax({
        url: '/dict/lemma/'+ lemma_id + '/' + label_id + '/add_label', 
        type: 'GET',
        success: function(){
            $("#row-"+lemma_id).hide();
        },
    }); 
}

function showAudioInfo() {
   $("body").on("click", ".audio-info-caller", function(event) {
//console.log('click');       
        event.preventDefault(); // reload event after AJAX reload
        var audio_id = $(this).attr('id');
//console.log('info-'+audio_id);        
        $(".audio-info").hide(); // hide all open blocks
        $("#info-"+audio_id).show('slow');
    });
        
    $(document).mouseup(function (e){
        var div = $(".audio-info");
        if (!div.is(e.target)
            && div.has(e.target).length === 0) {
                div.hide(); // скрываем его
        }
    });    
}

function callCreatePhonetic() {
    $("#modalCreatePhonetic").modal('show');    
}

function createPhonetic() {
}

function suggestTemplates() {
    $.ajax({
        url: '/dict/lemma/suggest_templates', 
        type: 'GET',
        data: {
            lang_id: $( "#lang_id option:selected" ).val(), 
            lemma: $( "#lemma" ).val(),
            pos_id: $( "#pos_id option:selected" ).val(),
            dialect_id: $( "#wordform_dialect_id option:selected" ).val(),
            is_reflexive: $('#reflexive').prop('checked') ? 1 : 0,
        },
        success: function(result){            
            $("#choose_template").html(result);
            $("#modalSuggestTemplates").modal('show');    
        },
        error: function() {
        }
    }); 
}   

function insertTemplate(template) {
    $("#modalSuggestTemplates").modal('hide');    
    $("#lemma").val(template);
}
