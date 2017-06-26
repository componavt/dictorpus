function posSelect() {
    $("#lemma_pos_id")
        .change(function () {
            if ($( "#lemma_pos_id option:selected" ).val()==11) {
                $("#reflexive-field").show().prop("disabled", false);
            } else {
                $("#reflexive-field").hide().attr('checked',false).prop("disabled", true);
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