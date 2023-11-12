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

function addExample(i, relevance) {
    var id = $(i).data('add');
    var button = $(i);
    $.ajax({
        url: '/dict/meaning/example/add/' + id + '/' + relevance, 
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

function reloadExamplesForId(id, locale='ru') {
    $("#meaning-examples_"+ id).empty();
    $("#img-loading_"+ id).show();
    $.ajax({
        url: '/' + locale + '/dict/meaning/examples/load/'+ id, 
        data: {update_examples: 1},
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

function reloadExamples(i,locale) {
    var id = $(i).data('reload');
    reloadExamplesForId(id,locale);
}   


function loadExamples(route, id, start, update_examples) {
//alert(route+'/'+id);    
    $("#img-loading_"+ id).show();    
    $.ajax({
        url: route+'/'+id, 
        data: {start: start,
               update_examples: update_examples},
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

function showMoreExamples(i, start, locale, is_edit=1) {    
    var id = $(i).attr('data-for');
    $("#img-loading-more_"+ id).show();
    
    $(i).hide();
    $.ajax({
        url: '/' + locale + '/dict/meaning/examples/load_more/'+ id + '?is_edit='+is_edit, 
        data: {start: start},
        type: 'GET',
        success: function(result){
            $("#more-"+ id).append(result);
            $("#more-"+ id).show();
            $("#img-loading-more_"+ id).hide();
            $("#hide-more-"+ id).show();
        },
        error: function() {
            $("#more-"+ id).append('ERROR');
/*        error: function(jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: '+route+'/'+id;
//alert(text);
            $("#meaning-examples_"+ id).html(text);*/
            $("#img-loading-more_"+ id).hide();
        }
    }); 
}

function hideExamples(meaning_id) {    
    $('#more-'+meaning_id).hide();
    $('#hide-more-'+meaning_id).hide();
    
    $('#show-more-'+meaning_id).show();
}

function showExamples(meaning_id) {    
    $('#more-'+meaning_id).show();
    $('#hide-more-'+meaning_id).show();
    
    $('#show-more-'+meaning_id).hide();
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

function loadPhoto(obj, id, url, with_url=1) {
    $("#img-photo-loading_"+ id).show();                
    $('#'+obj+'-photo_'+ id).html('');
    $.ajax({
        url: url+'?with_url='+with_url, 
        type: 'GET',
        success: function(result){
            $('#'+obj+'-photo_'+ id).html(result);
            $("#img-photo-loading_"+ id).hide();     
        },
        error: function() {
            $("#img-photo-loading_"+ id).hide();                
            $('#'+obj+'-photo_'+ id).html('ERROR');
        }
    }); 
}

