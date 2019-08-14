function reloadWordforms(i) {
    var id = $(i).data('reload');
    $("#wordforms").empty();
    $("#img-loading_wordforms").show();
    $.ajax({
        url: '/dict/lemma_wordform/'+ id + '/reload/', 
        type: 'GET',
        success: function(result){
            $("#wordforms").html(result);
            $("#img-loading_wordforms").hide();                
        },
 /*       error: function() {
            $("#wordforms").html('ERROR'); */
        error: function(jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+')'; 
            $("#meaning-examples_"+ id).html(text);
            $("#img-loading_wordforms").hide();                
        }
    }); 
}   

