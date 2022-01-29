function addTranslation(sentence_id, w_id) {
    $(".add-translation").click(function(){
        var selected_lang = $( "#lang_id_for_new option:selected" );
        var lang_id = selected_lang.val();
        $.ajax({
            url: '/corpus/sentence/' + sentence_id + '/translation/' + w_id + '_' + lang_id + '/create', 
            type: 'GET',
            success: function(result){
                $("#translations").append(result);
                selected_lang.remove();
                if ($('#lang_id_for_new > option').length === 0) {
                    $("#add-translation-div").hide();
                }
            },
            error: function() {
            }
        });         
    });
}

function saveTranslation(sentence_id, w_id, lang_id, action) {
    $.ajax({
        url: '/corpus/sentence/' + sentence_id + '/translation/' + w_id + '_' + lang_id + '/' + action, 
        type: 'GET',
        data: {
          'text': $("#translations_for_" + lang_id).val()
        },
        success: function(result){
            $("#translation_" + lang_id).html(result);  //.removeClass('row')
        },
//        error: function() {
        error: function(jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+')'; 
            $("#translation_" + lang_id).html(text);
        }
    });     
}

function editTranslation(sentence_id, w_id, lang_id) {
    $.ajax({
        url: '/corpus/sentence/' + sentence_id + '/translation/' + w_id + '_' + lang_id + '/edit', 
        type: 'GET',
        success: function(result){
            $("#translation_" + lang_id).html(result);
        },
        error: function() {
            $("#translation_" + lang_id).html('ERROR');
        }
    });     
}

function editFragment(sentence_id, w_id) {
    $.ajax({
        url: '/corpus/sentence/' + sentence_id + '/fragment/' + w_id + '/edit', 
        type: 'GET',
        success: function(result){
            $("#fragment").html(result);
        },
        error: function() {
            $("#fragment").html('ERROR');
        }
    });     
}

function saveFragment(sentence_id, w_id) {
    $.ajax({
        url: '/corpus/sentence/' + sentence_id + '/fragment/' + w_id + '/update', 
        type: 'GET',
        data: {
          'text_xml': $("#fragment_text").val()
        },
        success: function(result){
            $("#fragment").html(result);  
        },
        error: function() {
            $("#fragment").html('ERROR');
        }
    });     
}

