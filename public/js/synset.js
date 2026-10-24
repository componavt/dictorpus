function setStatus(id) {
    var new_status = $("#status-" + id).attr("data-new");
    var old_status = $("#status-" + id).attr("data-old");
    //console.log(new_status, old_status);    
    $.ajax({
        url: '/dict/synset/' + id + '/set_status/' + new_status,
        type: 'GET',
        success: function (status) {
            $("#status-" + id).removeClass('status' + old_status)
                .addClass('status' + status)
                .attr("data-new", old_status)
                .attr("data-old", status);
            /*            $("#status-"+id).addClass('status'+status);
                        $("#status-"+id).attr("data-new", old_status);
                        $("#status-"+id).attr("data-old", status);*/
            //console.log($("#status-"+id).attr("data-new"), $("#status-"+id).attr("data-old"));    
        },
        error: function () {
        }
    });
}

function removeMeaning(synset_id, meaning_id) {
    if (!synset_id) {
        $("#meanings_" + meaning_id + '__syntype_id_').prop('disabled', true);
        $("#meaning-" + meaning_id).css('display', 'none');
        return;
    }
    $.ajax({
        url: '/dict/synset/' + synset_id + '/remove_meaning/' + meaning_id,
        type: 'GET',
        success: function (result) {
            if (result) {
                $("#meanings_" + meaning_id + '__syntype_id_').prop('disabled', true);
                $("#meaning-" + meaning_id).css('display', 'none');
            }
        },
        error: function () {
        }
    });
}

function removeMeaningFromList(meaning_id) {
    $("#meanings_" + meaning_id + '__syntype_id_').prop('disabled', true);
    $("#meaning-" + meaning_id).css('text-decoration', 'line-through');
    $("#meaning_td_" + meaning_id).html('<i class="fa fa-plus-circle fa-lg add-to-list" onClick="addMeaningToList(' + meaning_id + ')" title="Добавить в синсет"></i>&nbsp;');
    $("#dominant-" + meaning_id).hide();
}


function addMeaningToList(meaning_id) {
    $("#meanings_" + meaning_id + '__syntype_id_').prop('disabled', false);
    $("#meaning_td_" + meaning_id).html('<i class="fa fa-trash fa-lg remove-from-list" onClick="removeMeaningFromList(' + meaning_id + ')" title="Удалить из синсета"></i>&nbsp;');
    $("#meaning-" + meaning_id).css('text-decoration', 'none');
    $("#dominant-" + meaning_id).show();
}

function reloadPotentialMembers(synset_id, lang_id) {
    var comment = $("#comment").val();
    $.ajax({
        url: '/dict/synset/' + synset_id + '/edit/potential_members',
        type: 'GET',
        data: {
            lang_id: lang_id,
            pos_id: $('#pos_id option:selected').val(),
            comment: comment,
            without: collectIds('.meanings', 'id')
        },
        success: function (result) {
            $("#potential-members").html(result);
        },
        error: function () {
        }
    });
}

function addMember() {
    $.ajax({
        url: '/dict/synset/edit/new_members',
        type: 'GET',
        data: {
            meanings: selectedValuesToURL("#new_members")
        },
        success: function (result) {
            $("#new-members").append(result);
            $("#pos_id").prop('disabled', true);
            $(".select-member").val(null).trigger('change');
        },
        error: function () {
        }
    });
}

/**
 * Восстанавливает и сохраняет представление сочетаемости для синсета.
 */
function initSynsetCollocationsView() {
    var container = $('#synset-collocations');

    if (!container.length) {
        return;
    }

    var synset_id = container.attr('data-synset-id');

    if (!synset_id) {
        return;
    }

    var storage_key = 'vepkar.synset.' + synset_id + '.collocations.view';
    var buttons = container.find('.synset-collocations-view');
    var saved_view = 'words';

    /**
     * Переключает панели и при необходимости запоминает выбранный режим.
     */
    function setView(view, persist) {
        view = view === 'matrix' ? 'matrix' : 'words';

        container.find('#synset-collocations-words')
            .prop('hidden', view === 'matrix');

        container.find('#synset-collocations-matrix')
            .prop('hidden', view !== 'matrix');

        buttons.each(function () {
            var button = $(this);
            var is_active = button.attr('data-view') === view;

            button
                .toggleClass('active', is_active)
                .attr('aria-pressed', is_active ? 'true' : 'false');
        });

        if (persist) {
            try {
                window.localStorage.setItem(storage_key, view);
            } catch (error) {
                // При запрете хранилища переключение продолжает работать.
            }
        }
    }

    try {
        if (window.localStorage.getItem(storage_key) === 'matrix') {
            saved_view = 'matrix';
        }
    } catch (error) {
        // При недоступном хранилище используем представление по словам.
    }

    buttons
        .off('click.synsetCollocationsView')
        .on('click.synsetCollocationsView', function () {
            setView($(this).attr('data-view'), true);
        });

    setView(saved_view, false);
}