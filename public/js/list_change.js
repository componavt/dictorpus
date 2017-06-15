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