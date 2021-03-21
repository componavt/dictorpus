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

/**
 *  show/hide a block with meanings and gramsets by click on a word
 */
function showLemmaLinked(text_id) {
   $(".lemma-linked").click(function(event) {
        event.preventDefault(); // reload event after AJAX reload
        var w_id = $(this).attr('id');
//console.log('w_id: '+w_id);        
        $(".links-to-lemmas").hide(); // hide all open blocks
        var w_block = $("#links_"+w_id);
//console.log(w_block);        
        w_block.show();
        var downloaded = w_block.data('downloaded');
        if (downloaded === 0) {
//console.log("showLemmaLinked: text_id, w_id: " + text_id + ','+ w_id );
            loadWordBlock(text_id, w_id, '/corpus/word/load_word_block/');
        }
    });
        
    $(document).mouseup(function (e){
        var div = $(".links-to-lemmas");
        if (!div.is(e.target)
            && div.has(e.target).length === 0) {
                div.hide(); // скрываем его
        }
    });    
}

function updateWordBlock(text_id, w_id) {
    loadWordBlock(text_id, w_id, '/corpus/word/update_word_block/');
    $("w[id="+w_id+"]").removeClass('meaning-checked').removeClass('gramset-checked').addClass('meaning-not-checked gramset--not-checked');
    
}

function loadWordBlock(text_id, w_id, url) {
    $("#links_"+w_id+".links-to-lemmas .img-loading").show();
//console.log("loadWordBlock: " + url + text_id + '_' + w_id);
    $.ajax({
        url: url + text_id + '_' + w_id, 
//        data: data,
        type: 'GET',
        success: function(result){
            $("#links_"+w_id+".links-to-lemmas").html(result);
            $("#links_"+w_id+".links-to-lemmas").data('downloaded', 1)
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    }); 
}

function saveLemma(text_id, data) {
    $.ajax({
        url: '/dict/lemma/store_simple', 
        data: data,
        type: 'GET',
        success: function(lemma_id){
            var opt = new Option(data.lemma, lemma_id);
            $('#choose-lemma').append(opt).trigger('change');
            opt.setAttribute('selected','selected');
            loadLemmaData(lemma_id, text_id);            
            $("#modalAddLemma").modal('hide');
            $("#choose-wordform").focus();
            $("#new_meanings_0__meaning_text__2_" ).val(null);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    }); 
}

function addLemma(text_id, lang_id) {
    $("#call-add-lemma").click(function() {
        $("#modalAddLemma").modal('show'); 
        var wordform = $( "#choose-wordform" ).val();
        $( "#lemma" ).val(wordform);
    });
    
    $("#save-lemma").click(function(){
        var data = {lang_id: lang_id, lemma: $( "#lemma" ).val(),
                    pos_id: $( "#pos_id option:selected" ).val(),
                    meaning: $( "#new_meanings_0__meaning_text__2_" ).val(),
                    wordform_dialect_id: $( "#dialect_id option:selected" ).val(),
                    number: $( "#number option:selected" ).val(),
                    reflexive: $( "#reflexive" ).val(),
                    impersonal: $( "#impersonal" ).val()};
        saveLemma(text_id, data);
    });
    
    $("#modalAddLemma .close, #modalAddLemma .cancel").on('click', function() {
        $( "#new_meanings_0__meaning_text__2_" ).val(null);
    });
    
}

function editWord(text_id, w_id, old_wordform) {
    $("#call-edit-word").click(function() {
        $("#editWordSentence").html( $("#addWordformSentence").html() );
        $("#modalEditWord").modal('show'); 
        var wordform = $( "#choose-wordform" ).val();
        $( "#word" ).val(wordform);
    });
    
    $("#save-word").click(function(){
        var word = $( "#word" ).val();
        if (word !== old_wordform) {
            saveWord(text_id, w_id, word);
        } else {
            $("#modalEditWord").modal('hide');
            $("#choose-wordform").focus();
        }
    });
        
}

function saveWord(text_id, w_id, word) {
    $.ajax({
        url: '/corpus/word/edit/' + text_id + '_' + w_id + '/', 
        data: {word: word},
        type: 'GET',
        success: function(){
            location.reload();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    }); 
}


function loadDataToWordformModal(text_id, w_id, wordform, lang_id) {
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
                        loadLemmaData(lemma_id, text_id);
                        $("#prediction-content").html(null);                          
                    }
                })
                .change();    
            loadPrediction(wordform, lang_id);
        }
    });        
}

function loadPrediction(wordform, lang_id) {
    $("#prediction-block .waiting").show();
    $.ajax({
        url: '/corpus/word/prediction', 
        data: {uword: wordform, lang_id: lang_id},
        type: 'GET',
        success: function(result){
            $("#prediction-block .waiting").hide();
            $("#prediction-content").html(result);               
        },
        error: function() {
            $("#prediction-block .waiting").hide();
/*            alert('Ошибка загрузки предсказания'); */
        }
    }); 
}

function loadLemmaData(lemma_id, text_id) {
    $.ajax({
        url: '/dict/wordform/create', 
        data: {lemma_id: lemma_id, text_id: text_id},
        type: 'GET',
        success: function(result){
            $("#addWordformFields").html(result);               
        },
        error: function() {
            alert('ERROR');
        }
    }); 
}

/**
 * эта функция не вызывается, если кликнуть за пределы окна
 * 
 */
function clearWordformModal() {
    $("#addWordformFields").html(null);
    $("#addWordformSentence").html(null);
    $('#choose-lemma').val(null).trigger('change');    
    $("#prediction-content").html(null);  
//console.log($("#prediction-content").html());    
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

function changeWordBlock(text_id, w_id) {
    $("w[id="+w_id+"].call-add-wordform").removeClass('call-add-wordform').addClass('meaning-checked gramset-checked');
    $("w[id="+w_id+"]").append('<div class="links-to-lemmas" id="links_'+w_id+'" data-download="0"></div>');
    loadWordBlock(text_id, w_id, '/corpus/word/load_word_block/');
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
function saveWordform(text_id, w_id, lemma_id, wordform, meaning_id, gramset_id, dialects, prediction, interpretation) {
    var route = '/dict/lemma_wordform/store';
    var test_url = '?text_id='+text_id+'&lemma_id='+lemma_id+'&meaning_id='+meaning_id+'&gramset_id='+gramset_id+'&wordform='+ wordform +'&w_id='+w_id+'&dialects[]='+dialects+'&prediction='+prediction+'&interpretation='+interpretation;
//console.log("saveWordform: " + test_url);
    
    $.ajax({
        url: route, 
        data: {text_id: text_id, 
               w_id: w_id,
               lemma_id: lemma_id, 
               wordform: wordform, 
               meaning_id: meaning_id,
               gramset_id: gramset_id,
               dialects: dialects,
               prediction: prediction,
               interpretation: interpretation
              },
        type: 'GET',
        success: function(result){       
            $("#modalAddWordform").modal('hide');
            changeWordBlock(text_id, w_id);
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
        
        loadDataToWordformModal(text_id, w_id, wordform, lang_id);
        
        editWord(text_id, w_id, wordform);    
    });
    
    $("#modalAddWordform .close, #modalAddWordform .cancel").on('click', function() {
        clearWordformModal();
    });
/*    
    $(document).mouseup(function (e){ // событие клика по веб-документу
		var div = $("#modalAddWordform"); 
		if (!div.is(e.target) // если клик был не по нашему блоку
		    && div.has(e.target).length === 0) { // и не по его дочерним элементам
			clearWordformModal();
		}
	});*/
        
    $("#save-wordform").click(function(){
        var wordform = $( "#choose-wordform" ).val();
        var lemma_id = $( "#choose-lemma option:selected" ).val();
        var meaning_id = $( "#choose-meaning option:selected" ).val();
        var gramset_id = $( "#choose-gramset option:selected" ).val();
        var w_id = $("#addWordformSentence .word-marked").attr('id');
        var dialects_obj = $("input[name=choose-dialect]:checked");
        var dialects = [];
        var dialect;
        for (var i=0; i<dialects_obj.length; i++) {
            dialect = dialects_obj[i];
            dialects.push(dialects_obj[i].value);
        }
        var prediction = $("input[name=prediction]:checked").val();
        var interpretation = $( "#interpretation" ).val();
        if (!lemma_id && prediction && !interpretation) {
            alert('Вы забыли указать значение для предсказанной леммы!');
            $( "#interpretation" ).focus();
        } else {
            saveWordform(text_id, w_id, lemma_id, wordform, meaning_id, gramset_id, dialects, prediction, interpretation);
        }
    });
    
    addLemma(text_id, lang_id);    
}

function fillInterpretation(str) {
    $( "#interpretation" ).val(str);
}