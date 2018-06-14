function addWordform(text_id, lang_id) {
    $(".call-add-wordform").click(function() {
        w_id = $(this).attr('id');
        $("#modalAddWordform").modal('show');
        $.ajax({
            url: '/dict/lemma/wordform/create/', 
            data: {text_id: text_id, w_id: w_id },
            type: 'GET',
            success: function(result){
                $("#addWordformDiv").html(result);
                
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
        }); 
        
    });
    
    
}