function posSelect() {
    $("#pos_id")
        .change(function () {
            if ($( "#pos_id option:selected" ).val()==11) { // is verb
                $("#reflexive-field").show().prop("disabled", false);
            } else if ($( "#pos_id option:selected" ).val()==19) { // is phrase
                $("#phrase-field").show().prop("disabled", false);
            } else {
                $("#reflexive-field").hide().attr('checked',false).prop("disabled", true);
                $("#phrase-field").hide().prop("disabled", true);
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
            if ($( "#lang_id option:selected" ).val()==5) { // livvic
                $("#wordforms-field").show().prop("disabled", false);
            } else {
                $("#wordforms-field").hide().attr('checked',false).prop("disabled", true);
            }
          })
        .change();    
}

