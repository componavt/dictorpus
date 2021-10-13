/*******************************************
  Shows and hides the search form 
  ******************************************/
function toggleSearchForm() {
        $(".show-search-form").click(function(){
            $(".show-search-form").hide();
/*            $(".search-button-b").css('padding-top', 0);*/
            $(".ext-form").show("slow");
            /*css('display', 'table');*/
            $(".hide-search-form").show();
        });
        $(".hide-search-form").click(function(){
            $(".hide-search-form").hide();
            $(".ext-form").hide("slow");
/*            $(".search-button-b").css('padding-top', '25px');*/
            $(".show-search-form").show();
        });
}

function addWordformGramFields(el) {
//    $(".add-wordform-gram").click(function(){
        var count = el.getAttribute('data-count');
        var lang_id = $("#search_lang option:selected" ).val();
        var pos_id = $("#search_pos option:selected" ).val();
        $.ajax({
            url: '/dict/lemma/wordform_gram_form/', 
            data: {count: count, lang_id: lang_id, pos_id: pos_id},
            type: 'GET',
            success: function(result){
                $("#search-wordforms").append(result);
            }
        }); 
        el.style.display='none';
//    });    
}

function addSentenceWordsFields(el) {
    var count = el.getAttribute('data-count');
    $.ajax({
        url: '/corpus/sentence/word_gram_form/', 
        data: {count: count},
        type: 'GET',
        success: function(result){
            $("#search-words").append(result);
        }
    }); 
    el.style.display='none';
    $("#distance"+count+' input').prop( "disabled", false );
    $("#distance"+count).show();
}

function callChoosePOS(el) {
    var posCaller=el.getAttribute('data-for');
    $('#insertPosTo').val(posCaller);
    var poses = $('#'+el.getAttribute('data-for')).val().split("|");
    $('.choose-pos input:checked').prop( "checked", false );
    $.each( poses, function( k, v ) {
        $('#pos_'+v).prop( "checked", true );
//console.log('#pos_'+v);                
    });
    $("#modalChoosePOS").modal('show');    
}

function callChooseGram(el) {
    var gramCaller=el.getAttribute('data-for');
    $('#insertGramTo').val(gramCaller);
    $('.choose-gram input:checked').prop( "checked", false );
    
    var grams = [];
    $.each($('#'+el.getAttribute('data-for')).val().split(","), function( i, c ) {
//console.log(c.split("|"));
        $.each(c.split("|"), function (j, v) {
            $('#gram_'+v).prop( "checked", true );
        });
    });
    $("#modalChooseGram").modal('show');    
}

