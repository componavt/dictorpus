function addMeaning() {
    $(".add-new-meaning").click(function(){
        var count = $(this).data("count");
        var meaning_n = $(this).data('meaning_n');
        $.ajax({
            url: '/dict/meaning/create/', 
            data: {count: count, meaning_n: meaning_n},
            type: 'GET',
            success: function(result){
                $("#new-meanings").append(result);
            }
        }); 
        $(this).data('count',count + 1);
        $(this).data('meaning_n', meaning_n+1);
    });    
}

function addExample(i) {
    var id = $(i).data('add');
    var button = $(i);
    $.ajax({
        url: '/dict/meaning/example/add/'+id, 
        type: 'GET',
        success: function(result){
            $("#sentence-relevance_"+ id).html(result);
            button.hide();
        },
        error: function() {
            alert('error');
        }
    }); 
}    

function removeExample(i) {
    var id = $(i).data('for');
    $.ajax({
        url: '/dict/lemma/remove/example/'+id, 
        type: 'GET',
        success: function(result){
            if (result) {
                $("#sentence-"+ id).hide();
            }
        }
    });    
}    

function reloadExamplesForId(id) {
    $("#meaning-examples_"+ id).empty();
    $("#img-loading_"+ id).show();
    $.ajax({
        url: '/dict/meaning/examples/reload/'+ id, 
        type: 'GET',
        success: function(result){
            $("#meaning-examples_"+ id).html(result);
            $("#img-loading_"+ id).hide();                
        },
        error: function() {
            $("#meaning-examples_"+ id).html('ERROR'); 
/*        error: function(jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+')'; 
            $("#meaning-examples_"+ id).html(text);*/
            $("#img-loading_"+ id).hide();                
        }
    }); 
}    

function reloadExamples(i) {
    var id = $(i).data('reload');
    reloadExamplesForId(id);
}   


function loadExamples(route, id) {
//alert(route+'/'+id);    
    $("#img-loading_"+ id).show();    
    $.ajax({
        url: route+'/'+id, 
        type: 'GET',
        success: function(result){
            $("#meaning-examples_"+ id).html(result);
            $("#img-loading_"+ id).hide();                
        },
        error: function() {
            $("#meaning-examples_"+ id).html('ERROR');
/*        error: function(jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: '+route+'/'+id;
//alert(text);
            $("#meaning-examples_"+ id).html(text);*/
            $("#img-loading_"+ id).hide();                
        }
    }); 
}

function showExamples(i) {    
    var meaning_n = $(i).attr('data-for');
    var id='more-'+meaning_n;
    $(i).hide();
    $('#'+id).show();
}

function hideExamples(meaning_n) {    
    var text='more-'+meaning_n;
    var link='show-more-'+meaning_n;
    $('#'+text).hide();
    $('#'+link).show();
}

/*
 * Adds to the word the selected value of the lemma
 * 
 * @param String route - url to script
 * @returns NULL
 */
function addWordMeaning(el) {
    var id = el.getAttribute('data-add');
    var w_id = el.closest('w').getAttribute('id');

    $.ajax({
        url: '/corpus/text/add_example/'+id, 
        type: 'GET',
        success: function(result){
            $("#links_"+ w_id).html(result);
            $("w#"+w_id).removeClass('polysemy').removeClass('meaning-not-checked').addClass('meaning-checked');
        }
    });    
}    

/*
 * Adds to the word the selected gramset
 * 
 * @param string route - url to script
 * @returns NULL
 */
function addWordGramset(el) {
    
    var id = el.getAttribute('data-add');
    var w_id = el.closest('w').getAttribute('id');

    $.ajax({
        url: '/corpus/word/add_gramset/'+id, 
        type: 'GET',
        success: function(result){
            $("#links_"+ w_id).html(result);
            $("#gramsets_"+ w_id).removeClass('word-gramset-not-checked');
            $("w#"+w_id).removeClass('gramset-not-checked').addClass('gramset-checked');
        }
    }); 
}    

