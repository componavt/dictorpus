function callHelpLemma() {
    var lang_id=$( "#lang_id option:selected" ).val();
    if (lang_id) {
        $(".help-lemma-lang").hide();
        $("#help-lemma-lang-"+lang_id).show();        
        
        var pos_id=$( "#pos_id option:selected" ).val();
        const name_pos = ['1', '5'];
//console.log(pos_id);     
//console.log(name_pos.includes(pos_id));     
        if (pos_id) {
            $(".help-lemma-pos").hide();
            if (pos_id==='11') {
                $("#help-lemma-pos-verb").show();        
            } else if (name_pos.includes(pos_id)) {
                $("#help-lemma-pos-name").show();        
            }
        }
    }
    callHelp('help-lemma');    
}

function callHelp(id) {
    $(".help-section").hide();
    $("#"+id).show();
    $("#modalHelp").modal('show');    
}