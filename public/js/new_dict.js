/**
 * load creation form for meaning example in school dictionary
 * 
 * @param int meaning_id
 */
function addSimpleExample(meaning_id) {
    var button = $('#add-example-for-'+meaning_id);
    $.ajax({
        url: '/dict/example/create/' + meaning_id, 
        type: 'GET',
        success: function(result){
            $(result).insertBefore(button);
            button.hide();
        },
        error: function() {
            alert('error');
        }
    }); 
}    

/**
 * add new meaning example in school dictionary
 * 
 * @param int meaning_id
 */
function createExample(meaning_id) {
    var button = $('#add-example-for-'+meaning_id);
    var example_div = $('#new-example-'+meaning_id);
    var example = $('#example-new-for-'+meaning_id).val();
    var example_ru = $('#example_ru-new-for-'+meaning_id).val();
    $.ajax({
        url: '/dict/example/store/' + meaning_id, 
        data: {
          example: example,
          example_ru: example_ru,          
        },
        type: 'GET',
        success: function(result){
            $(result).insertBefore(example_div);
            example_div.remove();
            button.show();
        },
        error: function() {
            alert('error');
        }
    }); 
}    

/**
 * load edition form for meaning example in school dictionary
 * 
 * @param int example_id
 */
function editExample(example_id) {
    $.ajax({
        url: '/dict/example/' + example_id + '/edit', 
        type: 'GET',
        success: function(result){
            $("#b-example-"+ example_id).css('display','block').html(result);
        },
        error: function() {
            alert('error');
        }
    }); 
}    

/**
 * add new meaning example in school dictionary
 * 
 * @param int meaning_id
 */
function updateExample(example_id) {
    var example_div = $('#b-example-'+example_id);
    var example = $('#example-'+example_id).val();
    var example_ru = $('#example_ru-'+example_id).val();
    $.ajax({
        url: '/dict/example/'+example_id+'/update', 
        data: {
          example: example,
          example_ru: example_ru,          
        },
        type: 'GET',
        success: function(result){
            $(example_div).css('display','inline').html(result);
        },
        error: function() {
            alert('error');
        }
    }); 
}    

/**
 * remove label with meaning in school dictionary
 * 
 * @param int meaning_id
 */
function removeLabelMeaning(i, meaning_id, label_id, meaning_text) {
    if (confirm('Вы действительно хотите удалить значение "'+meaning_text+'"?')) {
        $.ajax({
            url: '/dict/meaning/'+meaning_id+'/remove_label/'+label_id, 
            type: 'GET',
            success: function(result){
                if (result) {
                    $("#meaning-"+meaning_id).remove();
                }
            },
            error: function() {
                alert('error');
            }
        }); 
    }
}

function addLemma(lang_id, label_id) {
    $("#call-add-lemma").click(function(e) {
        e.preventDefault();
        $("#modalAddLemma").modal('show'); 
    });
    
    $("#save-lemma").click(function(){
        var data = {lang_id: lang_id, 
                    label_id: label_id,
                    lemma: $( "#lemma" ).val(),
                    pos_id: $( "#pos_id option:selected" ).val(),
                    meaning: $( "#new_meanings_0__meaning_text__2_" ).val(),
                    wordform_dialect_id: $( "#dialect_id option:selected" ).val(),
                    number: $( "#number option:selected" ).val(),
                    reflexive: $( "#reflexive" ).prop('checked'),
                    impersonal: $( "#impersonal" ).prop('checked')};
        saveLemma(data);
    });
    
/*    $("#modalAddLemma .close, #modalAddLemma .cancel").on('click', function() {
        $( "#new_meanings_0__meaning_text__2_" ).val(null);
    }); */   
}

function saveLemma(data) {
    $("#save-lemma").attr("disabled", true);    
    $.ajax({
        url: '/dict/lemma/store_simple', 
        data: data,
        type: 'GET',
        success: function(lemma_row){
            $("#modalAddLemma").modal('hide');
            $("#lemma").val(null);
            $("#new_meanings_0__meaning_text__2_" ).val(null);
//            $("#pos_id option:selected" ).val(null);
            $("#save-lemma").attr("disabled", false);   
            $("#lemmasRows").prepend(lemma_row);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('saveLemma '+xhr.status);
            alert(thrownError);
            $("#save-lemma").attr("disabled", false);    
        }
    }); 
}

function addMeaning(lemma_id) {
    $.ajax({
        url: '/service/dict/meaning/'+lemma_id+'/create', 
        type: 'GET',
        success: function(result){
            $("#modalMeaning").modal('show'); 
/*            $("#{{$lemma->id}}-"+lemma_id).append(result);*/
        },
        error: function() {
            alert('error');
        }
    }); 
}    
