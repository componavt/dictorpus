function posSelect() {
    $("#pos_id")
        .change(function () {
            $(".lemma-feature-field").hide().prop("disabled", true);
            if ($("#pos_id option:selected" ).val()==11) { // is verb
                $("#reflexive-field").show().prop("disabled", false);
                $("#transitive-field").show().prop("disabled", false);
            } else if ($("#pos_id option:selected").val()==5 || $("#pos_id option:selected").val()==14) { // is noun or proper noun
                $("#animacy-field").show().prop("disabled", false);
                $("#abbr-field").show().prop("disabled", false);
                $("#plur_tan-field").show().prop("disabled", false);
            } else if ($( "#pos_id option:selected" ).val()==6) { // is numeral
                $("#numtype-field").show().prop("disabled", false);
            } else if ($( "#pos_id option:selected" ).val()==10) { // is pronoun
                $("#prontype-field").show().prop("disabled", false);
            } else if ($( "#pos_id option:selected" ).val()==2) { // is adverb
                $("#advtype-field").show().prop("disabled", false);
                $("#degree-field").show().prop("disabled", false);
            } else if ($( "#pos_id option:selected" ).val()==1) { // is adjective
                $("#degree-field").show().prop("disabled", false);
            } else if ($( "#pos_id option:selected" ).val()==19) { // is phrase
                $("#phrase-field").show().prop("disabled", false);
            }
          })
        .change();    
}

function chooseList(list_name, div_name, url) {
    $("#"+list1_name)
        .change(function () {
            var selected_val=$( "#"+ list_name +" option:selected" ).val();
            $("#"+div_name).load(url+selected_val);
        })
        .change();    
}

function selectedValuesToURL(varname) {
    var forURL = [];
    $( varname + " option:selected" ).each(function( index, element ){
        forURL.push($(this).val());
    });
    return forURL;
}

function langSelect() {
    $("#lang_id")
        .change(function () {
            //$('.select-dialect').val(null).trigger('change');    
/*
            var lang = $( "#lang_id option:selected" ).val();
            if (lang==5) { // livvic
                $("#wordforms-field").show().prop("disabled", false);
            } else {
                $("#wordforms-field").hide().attr('checked',false).prop("disabled", true);
            } */
          })
        .change();    
}

