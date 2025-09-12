function setStatus(id) {
    var new_status = $("#status-"+id).attr("data-new");
    var old_status = $("#status-"+id).attr("data-old");
//console.log(new_status, old_status);    
    $.ajax({
        url: '/dict/synset/'+ id + '/set_status/' + new_status, 
        type: 'GET',
        success: function(status){
            $("#status-"+id).removeClass('status'+old_status)
                            .addClass('status'+status)
                            .attr("data-new", old_status)
                            .attr("data-old", status);
/*            $("#status-"+id).addClass('status'+status);
            $("#status-"+id).attr("data-new", old_status);
            $("#status-"+id).attr("data-old", status);*/
//console.log($("#status-"+id).attr("data-new"), $("#status-"+id).attr("data-old"));    
        },
        error: function() {
        }
    }); 
}   

function removeMeaning(synset_id, meaning_id) {
    if (!synset_id) {
        $("#meanings_"+meaning_id+'__syntype_id_').prop('disabled', true);
        $("#meaning-"+meaning_id).css('display','none');
        return;
    } 
    $.ajax({
        url: '/dict/synset/'+synset_id+'/remove_meaning/' + meaning_id, 
        type: 'GET',
        success: function(result){
            if (result) {
                $("#meanings_"+meaning_id+'__syntype_id_').prop('disabled', true);
                $("#meaning-"+meaning_id).css('display','none');
            }
        },
        error: function() {
        }
    }); 
}

function removeMeaningFromList(meaning_id) {
    $("#meanings_"+meaning_id+'__syntype_id_').prop('disabled', true);
    $("#meaning-"+meaning_id).css('text-decoration','line-through');
    $("#meaning_td_"+meaning_id).html('<i class="fa fa-plus-circle fa-lg add-to-list" onClick="addMeaningToList('+meaning_id+')" title="Добавить в синсет"></i>&nbsp;');
}
    

function addMeaningToList(meaning_id) {
    $("#meanings_"+meaning_id+'__syntype_id_').prop('disabled', false);
    $("#meaning_td_"+meaning_id).html('<i class="fa fa-trash fa-lg remove-from-list" onClick="removeMeaningFromList('+meaning_id+')" title="Удалить из синсета"></i>&nbsp;');
    $("#meaning-"+meaning_id).css('text-decoration','none');
}

function reloadPotentialMembers(synset_id) {
    var comment = $("#comment").val();
    $.ajax({
        url: '/dict/synset/'+synset_id+'/edit/potential_members?comment='+comment, 
        type: 'GET',
        success: function(result){
            $("#potential-members").html(result);
        },
        error: function() {
        }
    }); 
}

