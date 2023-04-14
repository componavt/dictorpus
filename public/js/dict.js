function loadCount(el_selector, url){    
    $.ajax({
        url: url, 
        data: {},
        type: 'GET',
        success: function(num){       
            $(el_selector).html(num);
        },
        error: function () {
            $(el_selector).html('ERROR');
        }
    });
}