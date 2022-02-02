function addDistrict() {
    $("#modalAddDistrict").modal('show');
}

function saveDistrict() {
    var name_ru = $( "#modalAddDistrict #name_ru" ).val();
    var region_id = $( "#region_id" ).val();
    var foundation = $( "#foundation" ).val();
    var abolition = $( "#abolition" ).val();
    var route = '/corpus/district';
    var test_url = '?name_ru='+name_ru+'&region_id='+region_id+'&foundation='+foundation+'&abolition='+abolition+'&from_ajax=1';
//alert(route + test_url);    
    $.ajax({
        url: route, 
        data: {name_ru: name_ru, 
               region_id: region_id,
               foundation: foundation,
               abolition: abolition,
               from_ajax: 1
              },
        type: 'POST',
        success: function(district_id){       
/*console.log('qid: ' +qid);    */
            $("#modalAddDistrict").modal('hide');
            if (district_id) {
                var opt = new Option(name_ru, district_id);
                $("#district_id").append(opt);
                opt.setAttribute('selected','selected')
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: ' + route + test_url;
            alert(text);
        }
    }); 
}

function addPlace(parent_field) {
    $("#modalAddPlace").modal('show');
    $("#modalAddPlace #parent_place").val(parent_field);
console.log('parent_field: '+parent_field);    
console.log('parent_field: '+ $("#modalAddPlace #parent_place").val());    
}

function savePlace() {
    var region_id = $( "#modalAddPlace #region_id" ).val();
    var district_id = $( "#modalAddPlace #district_id" ).val();
    var name_ru = $( "#modalAddPlace #name_ru" ).val();
    var name_en = $( "#modalAddPlace #name_en" ).val();

    var parent_field = $("#modalAddPlace #parent_place").val();
console.log('parent_field: '+parent_field);    
    var route = '/corpus/place/store';
    var test_url = '?name_ru='+name_ru+'&name_en='+name_en+'&region_id='+region_id+'&district_id='+district_id;
//alert(route + test_url);    
    $.ajax({
        url: route, 
        data: {name_ru: name_ru, 
               name_en: name_en,
               region_id: region_id,
               district_id: district_id
              },
        type: 'GET',
        success: function(place){       
//console.log('place: ' +place);    
            $("#modalAddPlace").modal('hide');
            if (place[0]) {
                var opt = new Option(place[1], place[0]);
                $('#'+parent_field).append(opt).trigger('change');
                opt.setAttribute('selected','selected');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: ' + route + test_url;
            alert(text);
        }
    }); 
}

function addInformant() {
    $("#modalAddInformant").modal('show');
}

function saveInformant() {
    var name_ru = $( "#modalAddInformant #name_ru" ).val();
    var name_en = $( "#modalAddInformant #name_en" ).val();
    var birth_place_id = $( "#modalAddInformant #birth_place_id" ).val();
    var birth_date = $( "#modalAddInformant #birth_date" ).val();
    
    var route = '/corpus/informant/store';
    var test_url = '?name_ru='+name_ru+'&name_en='+name_en+'&birth_place_id='+birth_place_id+'&birth_date='+birth_date;
    $.ajax({
        url: route, 
        data: {name_ru: name_ru, 
               name_en: name_en,
               birth_place_id: birth_place_id,
               birth_date: birth_date
              },
        type: 'GET',
        success: function(informant){       
            $("#modalAddInformant").modal('hide');
            if (informant) {
                var newOption = new Option(informant[1], informant[0], false, false);
                $('#event_informants').append(newOption).trigger('change');
                newOption.setAttribute('selected','selected')
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: ' + route + test_url;
            alert(text);
        }
    }); 
}

function addAuthor(author_field) {
    $("#author_field").val(author_field);
    $("#modalAddAuthor").modal('show');
}

function saveAuthor() {
    var route = '/corpus/author/store';
    $.ajax({
        url: route, 
        data: $( "#modalAddAuthor input" ).serializeArray(),
        type: 'GET',
        success: function(author){       
            $("#modalAddAuthor").modal('hide');
            if (author) {
                modifyAuthorFierds(author);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
//console.log($( "#modalAddAuthor input" ).serializeArray());    
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+')';
            alert(text);
        }
    }); 
}

function modifyAuthorFierds(author) {
    var other_author_field = 'authors';
    var author_field = $("#author_field").val();

    var newOption = new Option(author[1], author[0], false, false);
    $('#'+author_field).append(newOption).trigger('change');
    newOption.setAttribute('selected','selected');

    if (author_field === 'authors') {
        other_author_field = 'trans_authors';
    }
    
    var newOption = new Option(author[1], author[0], false, false);
    $('#'+other_author_field).append(newOption).trigger('change');
}

function addRecorder() {
    $("#modalAddRecorder").modal('show');
}

function saveRecorder() {
    var name_ru = $( "#modalAddRecorder #name_ru" ).val();
    var name_en = $( "#modalAddRecorder #name_en" ).val();
    var route = '/corpus/recorder/store';
    var test_url = '?name_ru='+name_ru+'&name_en='+name_en;
    $.ajax({
        url: route, 
        data: {name_ru: name_ru, 
               name_en: name_en
              },
        type: 'GET',
        success: function(recorder_id){     
//console.log(recorder_id);            
            $("#modalAddRecorder").modal('hide');
            if (recorder_id) {
                var opt = new Option(name_ru, recorder_id);
                $("#event_recorders").append(opt).trigger('change');
                opt.setAttribute('selected','selected')
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: ' + route + test_url;
            alert(text);
        }
    }); 
}

function addTopic() {
    $("#genres option:selected").each(function( index, element ){
        $('#genre_id option[value="'+ $(this).val() +'"]').prop('selected', true);
    });
    $("#plots option:selected").each(function( index, element ){
        $('#plot_id').val($(this).val()); // option[value="'+ $(this).val() +'"]').prop('selected', true);
        $('#plot_id').trigger('change');
    });
    $("#modalAddTopic").modal('show');
}

function saveTopic() {
    var name_ru = $( "#modalAddTopic #name_ru" ).val();
    var name_en = $( "#modalAddTopic #name_en" ).val();
    var route = '/corpus/topic/store';
    var test_url = '?name_ru='+name_ru+'&name_en='+name_en;
    $.ajax({
        url: route, 
        data: {name_ru: name_ru, 
               name_en: name_en,
               plot_id: selectedValuesToURL("#plot_id")
              },
        type: 'GET',
        success: function(recorder_id){     
//console.log(recorder_id);            
            $("#modalAddTopic").modal('hide');
            if (recorder_id) {
                var opt = new Option(name_ru, recorder_id);
                $("#topics").append(opt).trigger('change');
                opt.setAttribute('selected','selected')
            }
            $( "#modalAddTopic #name_ru" ).val('');
            $( "#modalAddTopic #name_en" ).val('');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            var text = 'Ajax Request Error: ' + 'XMLHTTPRequestObject status: ('+jqXHR.status + ', ' + jqXHR.statusText+'), ' + 
               	       'text status: ('+textStatus+'), error thrown: ('+errorThrown+'), route: ' + route + test_url;
            alert(text);
        }
    }); 
}

