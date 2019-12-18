function deleteWordforms(id, meanings=[]) {
    $("#wordforms").empty();
    $("#img-loading_wordforms").show();
    $.ajax({
        url: '/dict/lemma_wordform/'+ id + '/delete_wordforms', 
        type: 'GET',
        success: function(result){
            $("#wordforms").html(result);
            $("#img-loading_wordforms").hide();                
            $(meanings).each(function(key, id) {
               reloadExamplesForId(this);
            });
        },
        error: function() {
            $("#wordforms").html('ERROR'); 
/*        error: function(jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+')'; 
            $("#wordforms").html(text);*/
            $("#img-loading_wordforms").hide();                
        }
    }); 
}  

function loadWordforms(id, url='load', meanings=[]) {
    $("#wordforms").empty();
    
    $("#img-loading_wordforms").show();
    $.ajax({
        url: '/dict/lemma_wordform/'+ id + '/' + url, 
        type: 'GET',
        success: function(result){
            $("#wordforms").html(result);
            $("#img-loading_wordforms").hide();                
            $(meanings).each(function(key, id) {
        //console.log(this);        
               reloadExamplesForId(this);
            });
        },
        error: function() {
            $("#wordforms").html('ERROR'); 
            $("#img-loading_wordforms").hide();                
        }
    }); 
}   

function reloadWordforms(i, attrs='', meanings=[]) {
    var id = $(i).data('reload');
    loadWordforms(id, 'reload/'+ attrs, meanings);
}   

function chooseDialectForGenerate(lemma_id) {
    $("#dialect_id")
        .change(function () {
            var selected_val=$( "#dialect_id option:selected" ).val();
            $("#generate-wordforms").attr('data-reload', lemma_id  + '_' + selected_val);
        })
        .change();    
}

function copyBases(lemma_id) {
    $.ajax({
        url: '/dict/lemma_wordform/'+ lemma_id + '/get_bases', 
        type: 'GET',
        success: function(result){
            result.forEach(function(item, i, result) {
                $('#bases_' + i + '_').val(item);
            });
        },
        error: function() {
        }
    }); 
}
    
function clearWordforms() {
    var wordforms = $('.wordform-field');
    $.each(wordforms, function(i, item) {
        $(item).val('');
    });
}
    
function fillWordforms(lemma_id, dialect_id, bases_len) {
    var bases = [];
    for (let i = 0; i < bases_len; i++) {
        bases[i] = $("#bases_"+i+"_").val();
    };
    for (let i = bases_len; i < 8; i++) {
        bases[i] = null;
    }
//    console.log(bases);
    var basesInJSON = JSON.stringify(bases);
    $.ajax({
        url: '/dict/lemma_wordform/'+ lemma_id + '_' + dialect_id + '/get_wordforms', 
        type: 'GET',
        data: {bases: basesInJSON},
        success: function(result){
    console.log(result);
            $.each(result, function(i, item) {
                var old_value = $('#lang_wordforms_' + i + '__' + dialect_id + '_').val();
                var new_value = item;
                if (old_value !== '' && old_value !== new_value) {
                    var new_value = old_value + ', ' + item;
                } 
                $('#lang_wordforms_' + i + '__' + dialect_id + '_').val(new_value);
            });
        },
        error: function() {
        }
    }); 
}
