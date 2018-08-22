function addWordform(text_id, lang_id) {
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
    $(".call-add-wordform").click(function() {
        w_id = $(this).attr('id');
        $("#modalAddWordform").modal('show');
        $.ajax({
            url: '/corpus/text/sentence', 
            data: {text_id: text_id, w_id: w_id },
            type: 'GET',
            success: function(result){
                $("#addWordformSentence").html(result);               
                $("#choose-lemma")
                    .change(function () {
                        var lemma_id=$( "#choose-lemma option:selected" ).val();
                        if (lemma_id != null) {
                        $.ajax({
                            url: '/dict/lemma/wordform/create', 
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
        
    });
    
    $("#save-wordform").click(function(){
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
//alert('/dict/lemma/wordform/update?text_id='+text_id+'&lemma_id='+lemma_id+'&meaning_id='+meaning_id+'&gramset_id='+gramset_id+'&w_id='+w_id+'&dialects='+dialects);        
        $.ajax({
            url: '/dict/lemma/wordform/update', 
            data: {text_id: text_id, 
                   w_id: w_id,
                   lemma_id: lemma_id, 
                   meaning_id: meaning_id,
                   gramset_id: gramset_id,
                   dialects: dialects
                  },
            type: 'GET',
            success: function(result){
//alert(result);                
/*                $(this).hide();*/
            }
        }); 
    });
    
}