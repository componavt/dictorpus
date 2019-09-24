function reloadWordforms(i, attrs='') {
    var id = $(i).data('reload');
//alert(id)    
    $("#wordforms").empty();
    $("#img-loading_wordforms").show();
    $.ajax({
        url: '/dict/lemma_wordform/'+ id + '/reload/' + attrs, 
        type: 'GET',
        success: function(result){
            $("#wordforms").html(result);
            $("#img-loading_wordforms").hide();                
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

function chooseDialectForGenerate(lemma_id) {
    $("#dialect_id")
        .change(function () {
            var selected_val=$( "#dialect_id option:selected" ).val();
            $("#generate-wordforms").attr('data-reload', lemma_id  + '_' + selected_val);
        })
        .change();    
}

function copyBases(id) {
    $.ajax({
        url: '/dict/lemma_wordform/'+ id + '/get_bases', 
        type: 'GET',
        success: function(result){
alert(result)            
//            $("#wordforms").html(result);
/*            $("#img-loading_wordforms").hide();                */
        },
        error: function() {
//            $("#wordforms").html('ERROR'); 
/*            $("#img-loading_wordforms").hide();                */
        }
    }); 
}
