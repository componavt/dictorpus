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

// load edition form for meaning example in school dictionary
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

// add new meaning example in school dictionary
function updateExample(example_id) {
    var example_div = $('#b-example-'+example_id);
    $.ajax({
        url: '/dict/example/'+example_id+'/update', 
        data: {
          example: $('#example-'+example_id).val(),
          example_ru: $('#example_ru-'+example_id).val(),          
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

// load edition form for meaning meaning in school dictionary
function editMeaning(meaning_id) {
    $.ajax({
        url: '/dict/meaning/' + meaning_id + '/edit', 
        type: 'GET',
        success: function(result){
            $("#b-meaning-"+ meaning_id).html(result);
        },
        error: function() {
            alert('error');
        }
    }); 
}    

// add new meaning meaning in school dictionary
function updateMeaning(meaning_id) {
    $.ajax({
        url: '/dict/meaning/'+meaning_id+'/update', 
        data: {
          meaning_text: $('#meaning_text-'+meaning_id).val(),
        },
        type: 'GET',
        success: function(result){
            $('#b-meaning-'+meaning_id).css('display','inline').html(result);
        },
        error: function() {
            alert('error');
        }
    }); 
}   

// remove label with meaning in a dictionary
function removeLabelMeaning(lemma_id, meaning_id, label_id, meaning_text) {
//    if (confirm('Вы действительно хотите удалить из списка значение "'+meaning_text+'"?')) {
        $.ajax({
            url: '/dict/meaning/'+meaning_id+'/remove_label/'+label_id, 
            type: 'GET',
            success: function(meanings){
                $("#meanings-"+lemma_id).html(meanings);
            },
            error: function() {
                alert('error');
            }
        }); 
//    }
}

function addLabelMeaning(lemma_id, meaning_id, label_id) {
    $.ajax({
        url: '/dict/meaning/'+meaning_id+'/add_label/'+label_id, 
        type: 'GET',
        success: function(meanings){
            $("#meanings-"+lemma_id).html(meanings);
        },
        error: function() {
            alert('error');
        }
    }); 
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
//                    meaning0: $( "#meaning0" ).val(),
                    wordform_dialect_id: $( "#dialect_id option:selected" ).val(),
                    number: $( "#number option:selected" ).val(),
                    reflexive: $( "#reflexive" ).prop('checked'),
                    impersonal: $( "#impersonal" ).prop('checked'),
                    meanings: {}
                };
        for (i=0; i<2; i++) {
            data['meanings'][i] = {
                meaning_text: $( "#meaning"+i ).val(),
                example: $( "#example"+i ).val(),
                example_ru: $( "#example_ru"+i ).val()
            };
        }        
//console.log(data);                
        saveLemma(data);
    });
    
/*    $("#modalAddLemma .close, #modalAddLemma .cancel").on('click', function() {
        $( "#new_meanings_0__meaning_text__2_" ).val(null);
    }); */   
}

function saveLemma(data) {
    $("#save-lemma").attr("disabled", true);    
    $.ajax({
        url: '/service/dict/lemma/store', 
        data: data,
        type: 'GET',
        success: function(lemma_row){
            $("#modalAddLemma").modal('hide');
            $("#lemma").val(null);
//            $("#pos_id option:selected" ).val(null);
            $("#save-lemma").attr("disabled", false);   
            $("#lemmasRows").prepend(lemma_row);
            for (i=0; i<2; i++) {
                $( "#meaning"+i ).val(null);
                $( "#example"+i ).val(null);
                $( "#example_ru"+i ).val(null);
            }        
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('saveLemma '+xhr.status);
            alert(thrownError);
            $("#save-lemma").attr("disabled", false);    
        }
    }); 
}

function addMeaning(lemma_id, label_id) {
    $.ajax({
        url: '/service/dict/meaning/'+lemma_id+'/create', 
        data: {label_id: label_id},
        type: 'GET',
        success: function(result){
            $("#modalAddMeaning").modal('show'); 
            $("#modalAddMeaning .modal-body").html(result);
        },
        error: function() {
            alert('error');
        }
    }); 
}    

function saveMeaning(label_id) {
    $("#save-meaning").attr("disabled", true);   
    var lemma_id = $("#modalAddMeaning #lemma_id").val();
    $.ajax({
        url: '/service/dict/meaning/'+lemma_id+'/store', 
        data: {
            label_id: label_id,
            meaning: $("#modalAddMeaning #meaning" ).val(),
            example: $("#modalAddMeaning #example" ).val(),
            example_ru: $("#modalAddMeaning #example_ru" ).val()
        },
        type: 'GET',
        success: function(meanings){
            $("#modalAddMeaning").modal('hide');
            $("#modalAddMeaning .modal-body").html(null);
            $("#save-meaning").attr("disabled", false);   
            $("#meanings-"+lemma_id).html(meanings);
        },
        error: function() {
            alert('error');
            $("#save-meaning").attr("disabled", false);    
        }
    }); 
}

function meaningUp(lemma_id, meaning_id, label_id) {
    $.ajax({
        url: '/dict/meaning/up/'+meaning_id, 
        data: {label_id: label_id},
        type: 'GET',
        success: function(meanings){
            $("#meanings-"+lemma_id).html(meanings);
        },
        error: function() {
            alert('error');
        }
    }); 
}    

function meaningDown(lemma_id, meaning_id, label_id) {
    $.ajax({
        url: '/dict/meaning/down/'+meaning_id, 
        data: {label_id: label_id},
        type: 'GET',
        success: function(meanings){
            $("#meanings-"+lemma_id).html(meanings);
        },
        error: function() {
            alert('error');
        }
    }); 
}    
