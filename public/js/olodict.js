function setClass(obj, class_name) {
    $("."+class_name).removeClass(class_name);
    obj.classList.add(class_name);    
}
function viewLetter(letter_obj) {
    $(".gram-active").removeClass('gram-active');
    $(".lemma-active").removeClass('lemma-active');

    setClass(letter_obj, 'letter-active');
    
    loadLemmas();
    loadGrams();
}

function viewGram(gram_obj) {
    setClass(gram_obj, 'gram-active');
    
    loadLemmas();
}

function viewLemma(lemma_obj) {
    setClass(lemma_obj, 'lemma-active');

    loadLemma(lemma_obj.getAttribute('data-id'));
}

function loadLemmas(page=1) {
    var first_letter = $(".letter-active").html();
    var gram = $(".gram-active").html();
    
    $.ajax({
        url: '/olodict/lemma_list', 
        data: {
            search_letter: first_letter,
            search_gram: gram,
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

