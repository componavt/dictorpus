function addWordform(text_id, lang_id) {
    $(".select-lemma2").select2({
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
    $(".call-add-wordform").click(function() {
        w_id = $(this).attr('id');
        $("#modalAddWordform").modal('show');
        $.ajax({
            url: '/corpus/text/sentence', 
            data: {text_id: text_id, w_id: w_id },
            type: 'GET',
            success: function(result){
                $("#addWordformSentence").html(result);               
            }
        }); 
        
    });
    
    
}