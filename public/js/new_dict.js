/**
 * load creation form for meaning example in school dictionary
 * 
 * @param int meaning_id
 */
function addSimpleExample(meaning_id) {
    var button = $('#add-example-for-'+meaning_id);
    $.ajax({
        url: '/dict/example/create/' + meaning_id, 
        type: 'GET',
        success: function(result){
            $(result).insertBefore(button);
            button.hide();
        },
        error: function() {
            alert('error');
        }
    }); 
}    

/**
 * add new meaning example in school dictionary
 * 
 * @param int meaning_id
 */
function createExample(meaning_id) {
    var button = $('#add-example-for-'+meaning_id);
    var example_div = $('#new-example-'+meaning_id);
    var example = $('#example-new-for-'+meaning_id).val();
    var example_ru = $('#example_ru-new-for-'+meaning_id).val();
    $.ajax({
        url: '/dict/example/store/' + meaning_id, 
        data: {
          example: example,
          example_ru: example_ru,          
        },
        type: 'GET',
        success: function(result){
            $(result).insertBefore(example_div);
            example_div.remove();
            button.show();
        },
        error: function() {
            alert('error');
        }
    }); 
}    

/**
 * load edition form for meaning example in school dictionary
 * 
 * @param int example_id
 */
function editExample(example_id) {
    $.ajax({
        url: '/dict/example/' + example_id + '/edit', 
        type: 'GET',
        success: function(result){
            $("#b-example-"+ example_id).css('display','block').html(result);
        },
        error: function() {
            alert('error');
        }
    }); 
}    

/**
 * add new meaning example in school dictionary
 * 
 * @param int meaning_id
 */
function updateExample(example_id) {
    var example_div = $('#b-example-'+example_id);
    var example = $('#example-'+example_id).val();
    var example_ru = $('#example_ru-'+example_id).val();
    $.ajax({
        url: '/dict/example/'+example_id+'/update', 
        data: {
          example: example,
          example_ru: example_ru,          
        },
        type: 'GET',
        success: function(result){
            $(example_div).css('display','inline').html(result);
        },
        error: function() {
            alert('error');
        }
    }); 
}    

/**
 * remove label with meaning in school dictionary
 * 
 * @param int meaning_id
 */
function removeLabelMeaning(i, meaning_id, label_id, meaning_text) {
    if (confirm('Вы действительно хотите удалить значение "'+meaning_text+'"?')) {
        $.ajax({
            url: '/dict/meaning/'+meaning_id+'/remove_label/'+label_id, 
            type: 'GET',
            success: function(result){
                if (result) {
                    $("#meaning-"+meaning_id).remove();
                }
            },
            error: function() {
                alert('error');
            }
        }); 
    }
}
