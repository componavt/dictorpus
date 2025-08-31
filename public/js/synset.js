function setStatus(id) {
    var new_status = $("#status-"+id).attr("data-new");
    var old_status = $("#status-"+id).attr("data-old");
//console.log(new_status, old_status);    
    $.ajax({
        url: '/dict/synset/'+ id + '/set_status/' + new_status, 
        type: 'GET',
        success: function(status){
            $("#status-"+id).removeClass('status'+old_status);
            $("#status-"+id).addClass('status'+status);
            $("#status-"+id).attr("data-new", old_status);
            $("#status-"+id).attr("data-old", status);
//console.log($("#status-"+id).attr("data-new"), $("#status-"+id).attr("data-old"));    
        },
        error: function() {
        }
    }); 
}   
