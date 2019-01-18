function addMeaning() {
    $(".add-new-meaning").click(function(){
        var count = $(this).data("count");
        var meaning_n = $(this).data('meaning_n');
        $.ajax({
            url: '/dict/lemma/meaning/create/', 
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

function reloadExamples(i) {
    var id = $(i).data('reload');
    $("#meaning-examples_"+ id).empty();
    $("#img-loading_"+ id).show();
    $.ajax({
        url: '/dict/meaning/examples/reload/'+ id, 
        type: 'GET',
        success: function(result){
            $("#meaning-examples_"+ id).html(result);
            $("#img-loading_"+ id).hide();                
        }
    }); 
}   

function loadExamples(route, id) {
    $("#img-loading_"+ id).show();
    $.ajax({
        url: route+'/'+id, 
        type: 'GET',
        success: function(result){
            $("#meaning-examples_"+ id).html(result);
            $("#img-loading_"+ id).hide();                
        },
        error: function() {
            alert('error');                
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

function addWordMeaning(route) {
    $(".choose-meaning").click(function(){
        var id = $(this).data('add');
        var w_id = $(this).closest('w').attr('id');
        
        $.ajax({
            url: route+'/'+id, 
            type: 'GET',
            success: function(result){
                $("#links_"+ w_id).html(result);
                $("w#"+w_id).removeClass('polysemy').addClass('has-checked');
            }
        }); 
    });    
}    
/*
function addExample(route) {
    $(".add-example").click(function(){
        var id = $(this).data('add');
        var button = $(this);
        $.ajax({
            url: route+'/'+id, 
            type: 'GET',
            success: function(result){
                $("#sentence-relevance_"+ id).html(result);
                button.hide();
            },
            error: function() {
                alert('error');
            }
        }); 
    });    
} 

function removeExample(route) {
    $(".remove-example").click(function(){
        var id = $(this).data('for');
        $.ajax({
            url: route+'/'+id, 
            type: 'GET',
            success: function(result){
                if (result) {
                    $("#sentence-"+ id).hide();
                }
            }
        }); 
    });    
}    

function reloadExamples(route) {
    $('.lemma-meaning').on('click','.reload-examples',function(){
        var id = $(this).data('reload');
        $("#meaning-examples_"+ id).empty();
        $("#img-loading_"+ id).show();   
        $.ajax({
            url: route+'/'+id, 
            type: 'GET',
            success: function(result){
                $("#meaning-examples_"+ id).html(result);
                $("#img-loading_"+ id).hide();                
            }
        }); 
    });    
}   

function toggleExamples() {    
    $('.show-more-examples').click(function(){
        var meaning_n = $(this).attr('data-for');
        var id='more-'+meaning_n;
        $(this).hide();
        $('#'+id).show();
    });
    $('.hide-more-examples').click(function(){
        var meaning_n = $(this).attr('data-for');
        var text='more-'+meaning_n;
        var link='show-more-'+meaning_n;
        $('#'+text).hide();
        $('#'+link).show();
    });
}

*/

