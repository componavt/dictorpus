function setClass(obj, class_name) {
    $("."+class_name).removeClass(class_name);
    obj.classList.add(class_name);    
}
function viewLetter(locale, letter_obj) {
    $(".gram-active").removeClass('gram-active');
    $(".lemma-active").removeClass('lemma-active');
    clearSearchForm();

    setClass(letter_obj, 'letter-active');
    
    loadLemmas(locale);
    loadGrams();
}

function viewGram(locale, gram_obj) {
    $("#search_template").val('');
    setClass(gram_obj, 'gram-active');
    clearSearchForm();
    
    loadLemmas();
}

function viewLemma(lemma_obj) {
    setClass(lemma_obj, 'lemma-active');

    loadLemma(lemma_obj.getAttribute('data-id'));
}

function resetSearchForm() {
    clearSearchForm();
    $('#search_pos').trigger('change');    
}

function clearSearchForm() {
    $("#search_template").val(null);
    $("#search_meaning").val(null);
    $('#search_pos').val(null);
    $('#with_audio').prop( "checked", false );
}

function searchLemmas(locale) {
    $(".letter-active").removeClass('letter-active');
    $(".gram-active").removeClass('gram-active');
    $(".lemma-active").removeClass('lemma-active');
    
    var with_audios='';
    if ($("#with_audios").is(':checked')) {
        with_audios = 1;
    }

    $.ajax({
        url: '/'+locale+'/olodict/lemma_list', 
        data: {
            search_template: $("#search_template").val(),
            search_meaning: $("#search_meaning").val(),
            search_pos: $("#search_pos").val(),
            with_audios: with_audios
              },
        type: 'GET',
        success: function(lemma_list){       
/*console.log('qid: ' +qid);    */
            $("#lemma-list").html(lemma_list);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: ' + route + test_url;
            alert(text);
        }
    }); 
}

function loadLemmas(locale, page=1) {
    $.ajax({
        url: '/'+locale+'/olodict/lemma_list', 
        data: {
            search_letter: $(".letter-active").html(),
            search_gram: $(".gram-active").html(),
            page: page
              },
        type: 'GET',
        success: function(lemma_list){       
/*console.log('qid: ' +qid);    */
            $("#lemma-list").html(lemma_list);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: ' + route + test_url;
            alert(text);
        }
    }); 
}

function loadGrams() {
    var first_letter = $(".letter-active").html();
    
    $.ajax({
        url: '/olodict/gram_links/' + first_letter, 
        type: 'GET',
        success: function(gram_links){       
/*console.log('qid: ' +qid);    */
            $("#gram-links").html(gram_links);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: ' + route + test_url;
            alert(text);
        }
    }); 
}

function loadLemma() {
    var lemma = $(".lemma-active").html();
    
    $.ajax({
        url: '/olodict/lemmas', 
        data: {
            search_lemma: lemma
              },
        type: 'GET',
        success: function(lemmas){       
/*console.log('qid: ' +qid);    */
            $("#lemmas-b").html(lemmas);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: ' + route + test_url;
            alert(text);
        }
    }); 
}

