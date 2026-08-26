var sourcePubpartRow = null;

function addPublication() {
    var year = $("#source_year").val();
    $("#modalAddPublication #authors").val($("#source_authors").val());
    $("#modalAddPublication #title").val($("#source_title").val());
    $("#modalAddPublication #year").val(year);
    $("#modalAddPublication .pubpart_year").val(year);
    $("#modalAddPublication").modal('show');
}

function addSourcePubpartRow(pubpart) {
    var $container = $('#source-pubparts');
    var $template = $('#source-pubpart-template');

    if (!$container.length || !$template.length) {
        return;
    }

    var index = parseInt($container.attr('data-next-index'), 10) || 0;

    var html = $template
        .html()
        .replace(/__INDEX__/g, index);

    var $row = $(html);
    var $select = $row.find('.select-pubpart');

    $container.append($row);
    $container.attr('data-next-index', index + 1);

    initSourcePubpartSelect($select);

    if (pubpart && pubpart.id) {
        var option = new Option(
            pubpart.text,
            pubpart.id,
            true,
            true
        );

        $select
            .append(option)
            .trigger('change');
    }

    return $row;
}

function initSourcePubpartSelect($select) {
    if ($select.hasClass('select2-hidden-accessible')) {
        return;
    }

    $select.select2({
        allowClear: true,
        placeholder: '',
        width: '100%',

        ajax: {
            url: '/corpus/pubpart/list',
            dataType: 'json',
            delay: 250,

            data: function (params) {
                return {
                    q: params.term,
                    publication_id: $('#source_publication_id').val()
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

function openPubpartModal($row) {
    var $publication = $('#source_publication_id');
    var publicationId = $publication.val();

    if (!publicationId) {
        alert('Сначала выберите публикацию.');
        return;
    }

    var $group = $('#source-pubparts-group');
    var isPeriodic = String(
        $group.data('is-periodic')
    ) === '1';

    var $modal = $('#modalAddPubpart');

    sourcePubpartRow = $row;

    $modal.find('#new_pubpart_publication_id').val(publicationId);
    $modal.find('#new_pubpart_title').val('');
    $modal.find('#new_pubpart_number').val('');
    $modal.find('#new_pubpart_issue_date').val('');
    $modal.find('#new_pubpart_year').val($('#source-pubparts-group').data('default-year') || '');

    $modal
        .find('.js-new-pubpart-periodic')
        .toggleClass('hidden', !isPeriodic);

    $modal
        .find('.js-new-pubpart-non-periodic')
        .toggleClass('hidden', isPeriodic);

    $modal.modal('show');
}

function savePublication() {
    var $modal = $('#modalAddPublication');
    var $button = $modal.find('#save-publication');

    var isPeriodic = $modal
        .find('input[name="is_periodic"]:checked')
        .val();

    if (typeof isPeriodic === 'undefined') {
        alert('Укажите, является ли публикация периодическим изданием.');
        return;
    }

    var title = $.trim($modal.find('#title').val());

    if (!title) {
        alert('Заполните заголовок публикации.');
        $modal.find('#title').focus();
        return;
    }

    var pubparts = getNewPublicationPubparts(
        $modal,
        String(isPeriodic) === '1'
    );

    $button.prop('disabled', true);

    $.ajax({
        url: '/corpus/publication/store',
        type: 'GET',
        dataType: 'json',

        data: {
            is_periodic: isPeriodic,
            authors: $modal.find('#authors').val(),
            title: title,
            year: $modal.find('#year').val(),

            addition_info: $modal.find('#addition_info').val(),

            new_pubparts: pubparts
        },

        success: function (publicationInfo) {
            $modal.modal('hide');

            if (publicationInfo && publicationInfo.id) {
                var $publicationSelect = $('#source_publication_id');

                var option = new Option(
                    publicationInfo.title,
                    publicationInfo.id,
                    true,
                    true
                );

                $publicationSelect
                    .append(option)
                    .trigger('change', [publicationInfo]);

                toggleSourcePubparts(publicationInfo);

                $.each(publicationInfo.pubparts || [], function (index, pubpart) {
                    addSourcePubpartRow(pubpart);
                });
            }

            resetPublicationModal($modal);
        },

        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Publication save error');
            console.error('HTTP status:', jqXHR.status);
            console.error('Text status:', textStatus);
            console.error('Error thrown:', errorThrown);
            console.error('Response:', jqXHR.responseText);

            alert(
                'Не удалось сохранить публикацию. ' +
                'HTTP-статус: ' + jqXHR.status
            );
        },

        complete: function () {
            $button.prop('disabled', false);
        }
    });
}

function savePubpart() {
    var $modal = $('#modalAddPubpart');
    var $button = $('#save-pubpart');

    var publicationId = $modal
        .find('#new_pubpart_publication_id')
        .val();

    var data = {
        publication_id: publicationId,
        title: $.trim($modal.find('#new_pubpart_title').val()),
        number: $.trim($modal.find('#new_pubpart_number').val()),
        year: $.trim($modal.find('#new_pubpart_year').val()),
        issue_date: $.trim($modal.find('#new_pubpart_issue_date').val())
    };

    $button.prop('disabled', true);

    $.ajax({
        url: '/corpus/pubpart/simple_store',
        type: 'GET',
        dataType: 'json',
        data: data,

        success: function (pubpart) {
            if (!sourcePubpartRow || !pubpart.id) {
                return;
            }

            var $select = sourcePubpartRow.find('.select-pubpart');

            var option = new Option(
                pubpart.text,
                pubpart.id,
                true,
                true
            );

            $select
                .append(option)
                .trigger('change');

            if (pubpart.year) {
                $('#source-pubparts-group').data(
                    'default-year',
                    pubpart.year
                );
            }
            $modal.modal('hide');
        },

        error: function (jqXHR) {
            console.error(jqXHR.responseText);
            alert('Не удалось сохранить часть публикации.');
        },

        complete: function () {
            $button.prop('disabled', false);
        }
    });
}

function getNewPublicationPubparts($modal, isPeriodic) {
    var pubparts = {};

    $modal.find('.js-pubpart-row').each(function () {
        var $row = $(this);

        var sequenceNumber = $.trim(
            $row.find('[name$="[sequence_number]"]').val()
        );

        var title = $.trim(
            $row.find('[name$="[title]"]').val()
        );

        var number = $.trim(
            $row.find('[name$="[number]"]').val()
        );

        var year = $.trim(
            $row.find('[name$="[year]"]').val()
        );

        var issueDate = $.trim(
            $row.find('[name$="[issue_date]"]').val()
        );

        /*
         * Пустые строки не передаём.
         *
         * Для книги / непериодического издания частью считается
         * заполненный заголовок раздела.
         *
         * Для периодики — заполненный номер выпуска.
         */
        if (!isPeriodic && !title) {
            return;
        }

        if (isPeriodic && !number) {
            return;
        }

        pubparts[sequenceNumber] = {
            sequence_number: sequenceNumber,
            title: title,
            number: number,
            year: year,
            issue_date: issueDate
        };
    });

    return pubparts;
}

function hideSourcePubpartsLoader() {
    var $group = $('#source-pubparts-group');

    $group
        .find('#source-pubparts-loader')
        .addClass('hidden');

    $group
        .find('#source-pubparts-content')
        .removeClass('hidden');
}

function resetPublicationModal($modal) {
    $modal.find('#authors').val('');
    $modal.find('#title').val('');
    $modal.find('#year').val('');
    $modal.find('#addition_info').val('');

    $modal.find('input[name="is_periodic"][value="0"]')
        .prop('checked', true)
        .trigger('change');

    resetPublicationPubparts($modal);
}

function initPublicationPeriodicFields() {
    var $modal = $('#modalAddPublication');
    var $radios = $modal.find('input[name="is_periodic"]');

    if (!$radios.length) {
        return;
    }

    function switchPublicationFields() {
        var isPeriodic = String(
            $radios.filter(':checked').val()
        ) === '1';

        $modal.find('.js-periodic')
            .toggleClass('hidden', !isPeriodic);

        $modal.find('.js-non-periodic')
            .toggleClass('hidden', isPeriodic);
    }

    $radios
        .off('change.publicationPeriodic')
        .on('change.publicationPeriodic', switchPublicationFields);

    $modal.find('#add-publication-pubpart')
        .off('click.publicationPubpart')
        .on('click.publicationPubpart', function () {
            addPublicationPubpartRow();
        });

    switchPublicationFields();
}

function initSourcePubparts() {
    var $container = $('#source-pubparts');

    if (!$container.length) {
        return;
    }

    var $publication = $('#source_publication_id');

    function initAllPubpartSelects($parent) {
        $parent.find('.select-pubpart').each(function () {
            initSourcePubpartSelect($(this));
        });
    }

    function clearSelectedPubparts() {
        $container.find('.select-pubpart').each(function () {
            var $select = $(this);

            $select
                .val(null)
                .empty()
                .append('<option value=""></option>')
                .trigger('change');
        });

        $container.find('input[name$="[pages]"]').val('');
    }

    initAllPubpartSelects($container);

    $('#add-source-pubpart')
        .off('click.sourcePubparts')
        .on('click.sourcePubparts', function () {
            addSourcePubpartRow();
        });

    $container
        .off('click.sourcePubparts', '.remove-source-pubpart')
        .on('click.sourcePubparts', '.remove-source-pubpart', function () {
            var $row = $(this).closest('.source-pubpart-row');
            var $select = $row.find('.select-pubpart');

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $row.remove();
        });

    $container
        .off('click.sourcePubparts', '.create-source-pubpart')
        .on('click.sourcePubparts', '.create-source-pubpart', function () {
            openPubpartModal(
                $(this).closest('.source-pubpart-row')
            );
        });

    $publication
        .off('change.sourcePubparts')
        .on('change.sourcePubparts', function (event, publicationInfo) {
            clearSelectedPubparts();

            var publicationId = $publication.val();

            if (!publicationId) {
                toggleSourcePubparts(null);
                return;
            }

            // Если публикация только что создана в modalAddPublication.
            if (publicationInfo) {
                toggleSourcePubparts(publicationInfo);
                return;
            }

            // Пользователь вручную выбрал существующую публикацию.
            loadSourcePublicationInfo(publicationId);
        });

    if ($publication.val()) {
        loadSourcePublicationInfo($publication.val());
    }
}

function loadSourcePublicationInfo(publicationId) {
    showSourcePubpartsLoader();

    return $.getJSON(
        '/corpus/publication/' + publicationId + '/info'
    ).done(function (publicationInfo) {
        if (String($('#source_publication_id').val()) !== String(publicationInfo.id)) {
            return;
        }
        toggleSourcePubparts(publicationInfo);
    }).fail(function () {
        $('#source-pubparts-group').addClass('hidden');
        alert('Не удалось загрузить сведения о публикации.');
    });
}

function toggleSourcePubparts(publicationInfo) {
    var $group = $('#source-pubparts-group');

    if (!publicationInfo || !publicationInfo.id) {
        $group.addClass('hidden');
        return;
    }

    var isPeriodic = String(publicationInfo.is_periodic) === '1';

    var columnTitle = isPeriodic
        ? $group.data('issues')
        : $group.data('sections');

    hideSourcePubpartsLoader();

    $group
        .data('is-periodic', isPeriodic ? '1' : '0')
        .data('default-year', publicationInfo.default_year || '')
        .removeClass('hidden')
        .find('.source-pubparts-column-title')
        .text(columnTitle.toLowerCase());
}

function setPublicationPubpartVisibility($row, isPeriodic) {
    $row.find('.js-periodic').toggleClass('hidden', !isPeriodic);
    $row.find('.js-non-periodic').toggleClass('hidden', isPeriodic);
}

function showSourcePubpartsLoader() {
    var $group = $('#source-pubparts-group');

    $group.removeClass('hidden');

    $group
        .find('#source-pubparts-loader')
        .removeClass('hidden');

    $group
        .find('#source-pubparts-content')
        .addClass('hidden');
}

function addPublicationPubpartRow() {
    var $modal = $('#modalAddPublication');
    var $container = $modal.find('#publication-pubparts');
    var $template = $modal.find('#publication-pubpart-template');

    var index = parseInt($container.attr('data-next-index'), 10) || 1;

    var html = $template
        .html()
        .replace(/__INDEX__/g, index);

    var $row = $(html);

    var isPeriodic = String(
        $modal.find('input[name="is_periodic"]:checked').val()
    ) === '1';

    setPublicationPubpartVisibility($row, isPeriodic);

    $container.append($row);
    $container.attr('data-next-index', index + 1);
}

function resetPublicationPubparts($modal) {
    var $container = $modal.find('#publication-pubparts');

    $container.empty();
    $container.attr('data-next-index', 1);

    addPublicationPubpartRow();
}

