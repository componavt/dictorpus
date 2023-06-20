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
    $.ajax({
        url: '/dict/example/store/' + meaning_id, 
        data: {
          example: $('#example-new-for-'+meaning_id).val(),
          example_ru: $('#example_ru-new-for-'+meaning_id).val(),          
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
                    lemma: $( "#modalAddLemma #lemma" ).val(),
                    pos_id: $( "#modalAddLemma #pos_id option:selected" ).val(),
//                    meaning0: $( "#meaning0" ).val(),
                    wordform_dialect_id: $( "#modalAddLemma #dialect_id option:selected" ).val(),
                    number: $( "#modalAddLemma #number option:selected" ).val(),
                    reflexive: $( "#modalAddLemma #reflexive" ).prop('checked'),
                    impersonal: $( "#modalAddLemma #impersonal" ).prop('checked'),
                    meanings: {}
                };
        for (i=0; i<2; i++) {
            data['meanings'][i] = {
                meaning_text: $( "#modalAddLemma #meaning"+i ).val(),
                example: $( "#modalAddLemma #example"+i ).val(),
                example_ru: $( "#modalAddLemma #example_ru"+i ).val()
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

function viewWordforms(lemma_id, dialect_id) {
    $.ajax({
        url: '/service/dict/wordforms/'+lemma_id, 
        data: {dialect_id: dialect_id},
        type: 'GET',
        success: function(wordforms){
            $("#modalViewWordforms .modal-body").html(wordforms);
            $("#modalViewWordforms").modal('show');
        },
        error: function() {
            alert('error');
        }
    }); 
}

function editLemma(lemma_id) {
    $.ajax({
        url: '/service/dict/lemma/'+lemma_id+'/edit', 
//        data: {label_id: label_id},
        type: 'GET',
        success: function(result){
            $("#modalEditLemma").modal('show'); 
            $("#modalEditLemma .modal-body").html(result);
        },
        error: function() {
            alert('error');
        }
    }); 
}    

function updateLemma() {
    $("#update-lemma").attr("disabled", true); 
    var lemma_id = $( "#modalEditLemma #lemma-id" ).attr('data-id');
console.log(lemma_id);   
    $.ajax({
        url: '/service/dict/lemma/'+lemma_id+'/update', 
        data: {lemma: $( "#modalEditLemma #lemma" ).val(),
                pos_id: $( "#modalEditLemma #pos_id option:selected" ).val(),
                wordform_dialect_id: $( "#modalEditLemma #dialect_id option:selected" ).val(),
                number: $( "#modalEditLemma #number option:selected" ).val(),
                reflexive: $( "#modalEditLemma #reflexive" ).prop('checked'),
                impersonal: $( "#modalEditLemma #impersonal" ).prop('checked'),
            },
        type: 'GET',
        success: function(lemma){
            $("#update-lemma").attr("disabled", false);   
            $("#modalEditLemma").modal('hide');
            $("#modalEditLemma .modal-body").html(null);
            $("#b-lemma-"+lemma_id).html(lemma);
        },
        error: function() {
            alert('error');
            $("#update-lemma").attr("disabled", false);    
        }
    }); 
}

