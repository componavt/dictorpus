function newListLoad(url, div, limit) {
    $.ajax({
        url: url, 
        data: {limit: limit},
        type: 'GET',
        success: function(result){
            $("#"+div).append(result);
        }
    }); 
}