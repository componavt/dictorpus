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

/**
 * Управляет выбором участников и фильтром различий матрицы.
 * Сохраняет настройки отдельно для каждого синсета.
 */
function initSynsetCollocationComparison() {
    var container = $('#synset-collocations');

    if (!container.length) {
        return;
    }

    var members = container.find('.synset-collocation-member-checkbox');
    var differences = container.find('#synset-collocations-only-differences');
    var synset_id = container.attr('data-synset-id');

    if (!members.length || !differences.length || !synset_id) {
        return;
    }

    var storage_prefix = 'vepkar.synset.' + synset_id + '.collocations.';
    var members_key = storage_prefix + 'members';
    var differences_key = storage_prefix + 'only-differences';

    var table = container.find('table.synset-collocations-matrix');
    var columns = table.find('.synset-collocation-column');
    var member_cells = table.find('[data-member-id]');
    var rows = table.find('.synset-collocation-data-row');
    var categories = table.find('.synset-collocation-category-row');

    var no_members = container.find('#synset-collocations-no-members');
    var no_records = container.find('#synset-collocations-no-records');
    var no_differences = container.find('#synset-collocations-no-differences');

    /**
     * Возвращает ID выбранных значений в порядке столбцов.
     */
    function selectedIds() {
        return members.filter(':checked').map(function () {
            return this.value;
        }).get();
    }

    /**
     * Сохраняет выбор участников и предпочтение фильтра различий.
     */
    function saveSettings(ids) {
        try {
            window.localStorage.setItem(
                members_key,
                JSON.stringify({
                    all: ids.length === members.length,
                    ids: ids
                })
            );

            window.localStorage.setItem(
                differences_key,
                differences.prop('checked') ? '1' : '0'
            );
        } catch (error) {
            // При запрете хранилища сравнение продолжает работать.
        }
    }

    /**
     * Пересчитывает столбцы, строки и заголовки по текущему выбору.
     */
    function applyComparison(persist) {
        var ids = selectedIds();
        var selected_count = ids.length;
        var selected_map = {};

        $.each(ids, function (index, id) {
            selected_map[id] = true;
        });

        differences.prop('disabled', selected_count < 2);

        var only_differences = selected_count >= 2
            && differences.prop('checked');

        member_cells.each(function () {
            var cell = $(this);

            cell.prop(
                'hidden',
                selected_map[cell.attr('data-member-id')] !== true
            );
        });

        var section_counts = {
            core: 0,
            periphery: 0
        };

        columns.each(function () {
            var column = $(this);

            if (selected_map[column.attr('data-member-id')] === true) {
                section_counts[column.attr('data-member-group')]++;
            }
        });

        table.find('[data-member-section]').each(function () {
            var section = $(this);
            var count = section_counts[section.attr('data-member-section')];

            section
                .prop('hidden', count === 0)
                .attr('colspan', Math.max(1, count));
        });

        categories.find('th').attr('colspan', selected_count + 1);

        var relevant_count = 0;
        var visible_count = 0;

        rows.each(function () {
            var row = $(this);

            var recorded_count = row.find(
                '.synset-collocation-cell[data-recorded="1"]'
            ).filter(function () {
                return selected_map[
                    $(this).attr('data-member-id')
                ] === true;
            }).length;

            var has_records = recorded_count > 0;

            if (has_records) {
                relevant_count++;
            }

            var show_row = has_records
                && (!only_differences || recorded_count < selected_count);

            row.prop('hidden', !show_row);

            if (show_row) {
                visible_count++;
            }
        });

        categories.each(function () {
            var category = $(this);
            var category_key = category.attr('data-category-key');

            var has_visible_rows = rows.filter(function () {
                return $(this).attr('data-category-key') === category_key
                    && !this.hidden;
            }).length > 0;

            category.prop('hidden', !has_visible_rows);
        });

        table.prop('hidden', visible_count === 0);

        no_members.prop('hidden', selected_count !== 0);

        no_records.prop(
            'hidden',
            !(selected_count > 0 && relevant_count === 0)
        );

        no_differences.prop(
            'hidden',
            !(only_differences
                && relevant_count > 0
                && visible_count === 0)
        );

        if (persist) {
            saveSettings(ids);
        }
    }

    /**
     * Восстанавливает выбор, исключая отсутствующих в синсете участников.
     */
    function restoreMembers() {
        try {
            var saved = JSON.parse(
                window.localStorage.getItem(members_key) || 'null'
            );

            if (!saved || !$.isArray(saved.ids)) {
                return;
            }

            if (saved.all === true) {
                members.prop('checked', true);
                return;
            }

            var saved_map = {};

            $.each(saved.ids, function (index, id) {
                if (typeof id === 'string' || typeof id === 'number') {
                    saved_map[String(id)] = true;
                }
            });

            var matching_count = members.filter(function () {
                return saved_map[this.value] === true;
            }).length;

            if (saved.ids.length > 0 && matching_count === 0) {
                return;
            }

            members.each(function () {
                this.checked = saved_map[this.value] === true;
            });
        } catch (error) {
            // При повреждённой настройке оставляем выбранными всех.
        }
    }

    restoreMembers();

    try {
        differences.prop(
            'checked',
            window.localStorage.getItem(differences_key) === '1'
        );
    } catch (error) {
        // При недоступном хранилище фильтр остаётся выключенным.
    }

    members
        .off('change.synsetCollocationComparison')
        .on('change.synsetCollocationComparison', function () {
            applyComparison(true);
        });

    differences
        .off('change.synsetCollocationComparison')
        .on('change.synsetCollocationComparison', function () {
            applyComparison(true);
        });

    container.find('#synset-collocations-select-all')
        .off('click.synsetCollocationComparison')
        .on('click.synsetCollocationComparison', function () {
            members.prop('checked', true);
            applyComparison(true);
        });

    container.find('#synset-collocations-clear-selection')
        .off('click.synsetCollocationComparison')
        .on('click.synsetCollocationComparison', function () {
            members.prop('checked', false);
            applyComparison(true);
        });

    applyComparison(false);
}

/**
 * Предупреждает об изменениях формы перед переходом из блока сочетаемости.
 */
function initSynsetCollocationNavigation() {
    var container = $('#synset-collocations');
    var form = $('#synset-edit-form');

    if (!container.length || !form.length) {
        return;
    }

    var saved_data = form.serialize();

    container
        .off('click.synsetCollocationNavigation', 'a')
        .on('click.synsetCollocationNavigation', 'a', function (event) {
            var link = $(this);
            var href = link.attr('href');
            var target = link.attr('target');

            if (!href || href.charAt(0) === '#') {
                return;
            }

            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
                return;
            }

            if (event.which && event.which !== 1) {
                return;
            }

            if (target && target !== '_self') {
                return;
            }

            if (form.serialize() === saved_data) {
                return;
            }

            if (!window.confirm(container.attr('data-leave-confirm'))) {
                event.preventDefault();
            }
        });
}