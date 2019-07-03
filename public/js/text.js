function highlightSentences() {
    $(".sentence").hover(function(){ // over
            var trans_id = 'trans' + $(this).attr('id');
            $("#"+trans_id).css('background','yellow');
        },
        function(){ // out
            $(".trans_sentence").css('background','none');
        }
    );
    
    $(".trans_sentence").hover(function(){ // over
            var text_id = $(this).attr('id').replace('transtext','text');
            $("#"+text_id).css('background','#a9eef8');
        },
        function(){ // out
            $(".sentence").css('background','none');
        }
    );    
}

function showLemmaLinked() {
   $(".lemma-linked").click(function() {
        var block_id = 'links_' + $(this).attr('id');
        $(".links-to-lemmas").hide();
        $("#"+block_id).show();
    });
        
    $(document).mouseup(function (e){
		var div = $(".links-to-lemmas");
		if (!div.is(e.target)
		    && div.has(e.target).length === 0) {
			div.hide(); // скрываем его
		}
    });    
}

function saveLemma(lang_id, lemma, pos_id, meaning, dialect_id) {
    $.ajax({
        url: '/dict/lemma/store_simple', 
        data: {lang_id: lang_id, 
               lemma: lemma, 
               pos_id: pos_id,
               meaning: meaning,
               dialect_id: dialect_id
              },
        type: 'GET',
        success: function(lemma_id){
            $("#modalAddLemma").modal('hide');
            //$( "#lemma" ).val('');
            $( "#new_meanings_0__meaning_text__2_" ).val(null);
            var newOption = new Option(lemma, lemma_id, false, false);
            $('#choose-lemma').append(newOption).trigger('change');
            $('#choose-lemma').trigger({
                type: 'select2:select',
                params: {
                    data: {id: lemma_id, text: lemma}
                }
            });
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    }); 
}

function addLemma(lang_id) {
    $("#call-add-lemma").click(function() {
        $("#modalAddLemma").modal('show'); 
        var wordform = $( "#choose-wordform" ).val();
        $( "#lemma" ).val(wordform);
    });
    
    $("#save-lemma").click(function(){
        var lemma = $( "#lemma" ).val();
        var pos_id = $( "#pos_id option:selected" ).val();
        var meaning = $( "#new_meanings_0__meaning_text__2_" ).val();
        var dialect_id = $( "#dialect_id option:selected" ).val();
//alert("/dict/lemma/store_simple?lang_id="+lang_id+"&lemma="+lemma+"&pos_id="+pos_id+"&meaning="+meaning);    
        saveLemma(lang_id, lemma, pos_id, meaning, dialect_id);
    });
    
    $("#modalAddLemma .close, #modalAddLemma .cancel").on('click', function() {
        $( "#new_meanings_0__meaning_text__2_" ).val(null);
    });
    
}

function loadDataToWordformModal(text_id, w_id, wordform) {
    $.ajax({
        url: '/corpus/text/sentence', 
        data: {text_id: text_id, w_id: w_id },
        type: 'GET',
        success: function(result){
            $("#addWordformSentence").html(result);               
            $("#choose-wordform").val(wordform);               
            $("#choose-lemma")
                .change(function () {
                    var lemma_id=$( "#choose-lemma option:selected" ).val();
                    if (lemma_id != null) {
                    $.ajax({
                        url: '/dict/wordform/create', 
                        data: {lemma_id: lemma_id, text_id: text_id },
                        type: 'GET',
                        success: function(result){
                            $("#addWordformFields").html(result);               
                        },
                        error: function() {
                            alert('ERROR');
                        }
                    }); 
/*                        $("#"+div_name).load(url+selected_val);*/
                    }
                })
                .change();    
        }
    });        
}

function clearWordformModal() {
    $("#addWordformFields").html(null);
    $('#choose-lemma').val(null).trigger('change');    
}

function changeLemmaList(lang_id) {
    $(".select-lemma").select2({
        width: '100%',
        ajax: {
          url: "/dict/lemma/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              lang_id: lang_id
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    }); 
}

function changeWordBlock(text_id, w_id, meaning_id) {
    $("w[id="+w_id+"].call-add-wordform").removeClass('call-add-wordform').addClass('has-checked');
    $("w[id="+w_id+"].has-checked").append('<div class="links-to-lemmas" id="links_'+w_id+'"></div>')
    $.ajax({
        url: '/corpus/text/word/create_checked_block', 
        data: {text_id: text_id, 
               w_id: w_id,
               meaning_id: meaning_id,
              },
        type: 'GET',
        success: function(result){
            $("#links_"+ w_id).html(result);
        }
    });     
}

/**
 * Sends data to server for saving of a word form.
 * Calls changeWordBlock.
 * Closes the window.
 * 
 * @param {Integer} text_id
 * @param {Integer} w_id - id of a word in the text
 * @param {Integer} lemma_id
 * @param {String} wordform 
 * @param {Integer} meaning_id
 * @param {Integer} gramset_id
 * @param {Array} dialects - array of dialect IDs 
 * @returns {undefined}
 */
function saveWordform(text_id, w_id, lemma_id, wordform, meaning_id, gramset_id, dialects) {
    var route = '/dict/lemma/wordform/update'
    var test_url = '?text_id='+text_id+'&lemma_id='+lemma_id+'&meaning_id='+meaning_id+'&gramset_id='+gramset_id+'&wordform='+ wordform +'&w_id='+w_id+'&dialects[]='+dialects;
//alert(route + test_url);  
    
    $.ajax({
        url: route, 
        data: {text_id: text_id, 
               w_id: w_id,
               lemma_id: lemma_id, 
               wordform: wordform, 
               meaning_id: meaning_id,
               gramset_id: gramset_id,
               dialects: dialects
              },
        type: 'GET',
        success: function(result){
            $("#modalAddWordform").modal('hide');
            changeWordBlock(text_id, w_id, meaning_id);
            clearWordformModal();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: ' + route + test_url;
            alert(text);
        }
    }); 
}
    
/**
 * Adds word forms to the dictionary for the unmarked word.
 * Opens a window after clicking on the unmarked (black) word.
 * Calls saveWordform().
 * 
 * @param Integer text_id 
 * @param Integer lang_id
 * @returns NULL
 */    
function addWordform(text_id, lang_id) {
    changeLemmaList(lang_id);
    $(".call-add-wordform").click(function() {
        if (!$(this).hasClass('call-add-wordform')) {
            return;
        }
        var w_id = $(this).attr('id');
        var wordform = $(this).html();        

        $("#modalAddWordform").modal('show');
        loadDataToWordformModal(text_id, w_id, wordform);
    });
    
    $("#modalAddWordform .close, #modalAddWordform .cancel").on('click', function() {
        clearWordformModal();
    });
    
    $("#save-wordform").click(function(){
        var wordform = $( "#choose-wordform" ).val();
        var lemma_id = $( "#choose-lemma option:selected" ).val();
        var meaning_id = $( "#choose-meaning option:selected" ).val();
        var gramset_id = $( "#choose-gramset option:selected" ).val();
//        var sentence_id = $("#addWordformSentence s").attr('id');
        var w_id = $("#addWordformSentence .word-marked").attr('id');
        var dialects_obj = $("input[name=choose-dialect]:checked");
//alert(dialects_obj);        
        var dialects = [];
        var dialect;
        for (i=0; i<dialects_obj.length; i++) {
            dialect = dialects_obj[i];
            dialects.push(dialects_obj[i].value);
        }
        saveWordform(text_id, w_id, lemma_id, wordform, meaning_id, gramset_id, dialects);
    });
    addLemma(lang_id);    
}