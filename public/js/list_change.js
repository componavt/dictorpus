function posSelect(is_full_form=true) {
    $("#pos_id")
        .change(function () {
            $(".lemma-feature-field").hide().prop("disabled", true);
            if ($("#pos_id option:selected" ).val()==11) { // is verb
                $("#reflexive-field").show().prop("disabled", false);
                $("#impersonal-field").show().prop("disabled", false);
                if (is_full_form) {
                    $("#transitive-field").show().prop("disabled", false);
                }
                $("#without_gram-field").show().prop("disabled", false);
            } else if ($("#pos_id option:selected").val()==5 || $("#pos_id option:selected").val()==14) { // is noun or proper noun or pronoun
                if (is_full_form) {
                    $("#animacy-field").show().prop("disabled", false);
                    $("#abbr-field").show().prop("disabled", false);
                }
                $("#number-field").show().prop("disabled", false);
                $("#without_gram-field").show().prop("disabled", false);
            } else if ($("#pos_id option:selected").val()==1) { // is adjective
                if (is_full_form) {
                    $("#degree-field").show().prop("disabled", false);
                }
                $("#number-field").show().prop("disabled", false);
                $("#without_gram-field").show().prop("disabled", false);
            } else if ($( "#pos_id option:selected" ).val()==10) { // is pronoun
                if (is_full_form) {
                    $("#prontype-field").show().prop("disabled", false);
                }
                $("#number-field").show().prop("disabled", false);
                $("#without_gram-field").show().prop("disabled", false);
            } else if (is_full_form) {
                if ($( "#pos_id option:selected" ).val()==6) { // is numeral
                    $("#numtype-field").show().prop("disabled", false);
                $("#without_gram-field").show().prop("disabled", false);
                } else if ($( "#pos_id option:selected" ).val()==2) { // is adverb
                    $("#advtype-field").show().prop("disabled", false);
                    $("#degree-field").show().prop("disabled", false);
                } else if ($( "#pos_id option:selected" ).val()==19) { // is phrase
                    $("#phrase-field").show().prop("disabled", false);
                    $("#comptype-field").show().prop("disabled", false);
                }
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

function langSelect(lang_var="lang_id") {
    $("#"+lang_var)
        .change(function () {
            //$('.select-dialect').val(null).trigger('change');    
/*
            var lang = $( "#lang_id option:selected" ).val();
            if (lang==5) { // livvic
                $("#wordforms-field").show().prop("disabled", false);
            } else {
                $("#wordforms-field").hide().attr('checked',false).prop("disabled", true);
            } */
          })
        .change();    
}

function selectWithLang(el, url, lang_var, placeholder='', allow_clear=false){
    $(el).select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: url,
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              lang_id: selectedValuesToURL("#"+lang_var)
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectDialect(lang_var, placeholder='', allow_clear=false, selector='.select-dialect'){
    selectWithLang('.select-dialect', "/dict/dialect/list", lang_var, placeholder, allow_clear);
}

function selectPhrase(placeholder='') {
    selectWithLang(".multiple-select-phrase", "/dict/lemma/list_with_pos_meaning", 'lang_id', placeholder);
}    

function selectVariants(placeholder='') {
    selectWithLang(".multiple-select-variants", "/dict/lemma/list_with_pos_meaning", 'lang_id', placeholder);
}

function selectGramset(lang_var, pos_var, placeholder='', allow_clear=false){
    $(".select-gramset").select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/dict/gramset/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              lang_id: $( "#"+lang_var+" option:selected" ).val(),
              pos_id: $( "#"+pos_var+" option:selected" ).val()
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectGenre(corpus_var='search_corpus', placeholder='', allow_clear=false){
    $(".multiple-select-genre").select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/corpus/genre/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              corpus_id: selectedValuesToURL("#" + corpus_var)
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectPlot(plot_var='.select-plot', genre_var='search_genre', placeholder='', allow_clear=false){
    $(plot_var).select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/corpus/plot/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              genre_id: selectedValuesToURL("#" + genre_var)
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectCycle(plot_var='.select-cycle', genre_var='search_genre', placeholder='', allow_clear=false){
    $(plot_var).select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/corpus/cycle/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              genre_id: selectedValuesToURL("#" + genre_var)
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectTopic(plot_var='search_plot', placeholder='', allow_clear=false){
    $(".select-topic").select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/corpus/topic/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              plot_id: selectedValuesToURL("#" + plot_var)
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectDistrict(region_var='search_region', placeholder='', allow_clear=false){
    $(".select-district").select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/corpus/district/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              region_id: $("#" + region_var).val()
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectPlace(district_var='search_district', region_var='search_region', placeholder='', allow_clear=false){
    $(".select-place").select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/corpus/place/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              region_id: $("#" + region_var).val(),
              district_id: selectedValuesToURL("#" + district_var)
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectBirthDistrict(region_var='search_birth_region', placeholder='', allow_clear=false){
    $(".select-birth-district").select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/corpus/district/birth_list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              region_id: $("#" + region_var).val()
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectBirthPlace(district_var='search_birth_district', region_var='search_region_birth', placeholder='', allow_clear=false){
    $(".select-birth-place").select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/corpus/place/birth_list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              region_id: $("#" + region_var).val(),
              district_id: selectedValuesToURL("#" + district_var)
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectConcept(category_var, pos_var, placeholder='', allow_clear=false, label_id=null, status_in_label=1){
    $(".select-concept").select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/dict/concept/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              category_id: $( "#"+category_var+" option:selected" ).val(),
              pos_id: $( "#"+pos_var+" option:selected" ).val(),
              label_id: label_id,
              status_in_label: status_in_label
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectConceptWithoutCategory(pos_var, placeholder='', allow_clear=false){
    $(".select-concept").select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/dict/concept/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              pos_id: $( "#"+pos_var+" option:selected" ).val()
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}

function selectMotive(motype_var='search_motype', parent_id='', placeholder='', allow_clear=false){
    var data = function (params) {
            return {
              q: params.term, // search term
              motype_id: selectedValuesToURL("#" + motype_var),
              parent_id: parent_id
            };
          };
    selectAjax("/corpus/motive/list", data, placeholder, allow_clear, ".select-motive");
}


function selectMotives(selector=".select-motive", genre_var='search_genre', placeholder='', allow_clear=false){
    var data = function (params) {
            return {
              q: params.term, // search term
              genre_id: selectedValuesToURL("#" + genre_var),
            };
          };
    selectAjax("/corpus/motive/list", data, placeholder, allow_clear, selector);
}
/*
function selectMotive(motype_var='search_motype', parent_id='', placeholder='', allow_clear=false){
    $(".select-motive").select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: "/corpus/motive/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              motype_id: selectedValuesToURL("#" + motype_var),
              parent_id: parent_id
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });   
}
 */
function selectAjax(route, data, placeholder, allow_clear, selector){
    $(selector).select2({
        allowClear: allow_clear,
        placeholder: placeholder,
        width: '100%',
        ajax: {
          url: route,
          dataType: 'json',
          delay: 250,
          data: data,
          processResults: function (result) {
            return {
              results: result
            };
          },          
          cache: true
        }
    });   
}


