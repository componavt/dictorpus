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

function addPlace() {
    $("#modalAddPlace").modal('show');
}

function savePlace() {
    var region_id = $( "#modalAddPlace #region_id" ).val();
    var district_id = $( "#modalAddPlace #district_id" ).val();
    var name_ru = $( "#modalAddPlace #name_ru" ).val();
    var name_en = $( "#modalAddPlace #name_en" ).val();
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
console.log('place: ' +place);    
            $("#modalAddPlace").modal('hide');
            if (place[0]) {
                var opt = new Option(place[1], place[0]);
                $("#event_place_id").append(opt).trigger('change');
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
console.log(recorder_id);            
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

