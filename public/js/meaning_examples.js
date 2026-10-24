/**
 * Подтверждает выбранное значение употребления.
 */
function checkExampleMeaning(el) {
    saveExampleAnnotation(el, 'meaning', {
        meaning_id: $(el).attr('data-meaning-id')
    });
}

/**
 * Подтверждает выбранную привязку грамсета.
 */
function checkExampleGramset(el) {
    saveExampleAnnotation(el, 'gramset', {
        gramset_id: $(el).attr('data-gramset-id'),
        wordform_id: $(el).attr('data-wordform-id')
    });
}

/**
 * Сохраняет разметку и заменяет блок его актуальной серверной версией.
 */
function saveExampleAnnotation(el, action, data) {
    var word = $(el).closest('w');
    var page = word.closest('.meaning-example-search');
    var block = word.children('.links-to-lemmas');

    if (!block.length || block.data('saving')) {
        return;
    }

    block.data('saving', true);
    block.find('.example-review-plus, .example-review-reset')
        .prop('disabled', true);

    data._token = page.attr('data-csrf-token');

    data.distance_from = page.attr('data-distance-from');
    data.distance_to = page.attr('data-distance-to');

    $.ajax({
        url: page.attr('data-word-block-url')
            + '/' + word.attr('id')
            + '/' + action,
        type: 'POST',
        data: data,
        success: function (html) {
            block.html(html).data('downloaded', 1);
        },
        error: function () {
            alert(page.attr('data-save-error'));
        },
        complete: function () {
            block.data('saving', false);
            block.find('.example-review-plus, .example-review-reset')
                .prop('disabled', false);
        }
    });

}

/**
 * Сбрасывает выбор значения и грамсета для употребления.
 */
function resetExampleAnnotation(el) {
    saveExampleAnnotation(el, 'reset', {});
}

/**
 * Подключает поиск значения партнёра и показ настроек парного поиска.
 */
function initExamplePartnerSelect() {
    var partner_select = $('#example-search-partner');
    var partner_options = $('#example-search-partner-options');

    if (!partner_select.length) {
        return;
    }

    partner_select.select2({
        width: '100%',
        allowClear: true,
        minimumInputLength: 2,
        placeholder: partner_select.attr('data-placeholder'),
        ajax: {
            url: partner_select.attr('data-search-url'),
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return data;
            }
        }
    });

    function updatePartnerOptions() {
        partner_options.toggle(Boolean(partner_select.val()));
    }

    partner_select.on('change', updatePartnerOptions);

    updatePartnerOptions();
}

/**
 * Обновляет кэш блоков после возвращения из редактора сочетаемостей.
 */
function initExampleCollocationLinks() {
    $(window).on('focus.exampleCollocations', function () {
        $('.meaning-example-sentence .links-to-lemmas')
            .data('downloaded', 0);
    });
}