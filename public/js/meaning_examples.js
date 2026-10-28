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
    data.source_w_id = word.attr('data-source-w-id');
    data.original = word.attr('data-original') || 0;
    data.fragment_id = String(data.original) === '1' ? 0 : word.attr('data-fragment-id');
    data.synset_id = page.attr('data-synset-id') || '';
    data.examples_return = page.attr('data-examples-return') || '';

    var word_key = word.attr('data-word-key') || word.attr('id');

    $.ajax({
        url: page.attr('data-word-block-url') + '/' + word_key + '/' + action,
        type: 'POST',
        data: data,
        success: function (html) {
            block.html(html).data('downloaded', 1);
            page.find('w[data-word-key]').filter(function () {
                return $(this).attr('data-word-key') === word_key;
            }).children('.links-to-lemmas').not(block)
                .hide()
                .data('downloaded', 0);

            page.find('.example-original-content')
                .data('downloaded', 0);
            var source_relevance = block
                .find('.example-review-block')
                .attr('data-source-relevance');

            if (source_relevance !== undefined) {
                updateExampleRating(
                    word.closest('.example-card'),
                    parseInt(source_relevance, 10)
                );
            }
            updateExampleCollocationMarks();
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
 * Загружает соседние значения при открытии списка и фильтрует их по лемме.
 */
function initExamplePartnerSelect() {
    var partner_select = $('#example-search-partner');

    if (!partner_select.length) {
        return;
    }

    var form = partner_select.closest('form');

    partner_select.select2({
        width: '100%',
        allowClear: true,
        minimumInputLength: 0,
        maximumInputLength: 100,
        placeholder: partner_select.attr('data-placeholder'),
        ajax: {
            url: partner_select.attr('data-search-url'),
            dataType: 'json',
            delay: 250,
            cache: false,
            data: function (params) {
                return {
                    q: params.term || '',
                    page: params.page || 1,
                    distance_from: form
                        .find('[name="distance_from"]')
                        .val(),
                    distance_to: form
                        .find('[name="distance_to"]')
                        .val(),
                    only_checked: form
                        .find('input[type="checkbox"][name="only_checked"]')
                        .prop('checked') ? 1 : 0,
                    same_gramset: form
                        .find('input[type="checkbox"][name="same_gramset"]')
                        .prop('checked') ? 1 : 0
                };
            },
            processResults: function (data) {
                return data;
            }
        }
    });

    form.on(
        'change',
        '[name="distance_from"], [name="distance_to"], '
        + 'input[type="checkbox"][name="only_checked"], '
        + 'input[type="checkbox"][name="same_gramset"]',
        function () {
            partner_select.select2('close');
        }
    );
}

/**
 * Сбрасывает кэш разборов при возврате фокуса и обновляет страницу после bfcache.
 */
function initExampleCollocationLinks() {
    $(window)
        .off('focus.exampleCollocations')
        .on('focus.exampleCollocations', function () {
            $('.meaning-example-sentence .links-to-lemmas')
                .data('downloaded', 0);

            $('.example-original-content')
                .data('downloaded', 0);
        });

    $(window)
        .off('pageshow.exampleCollocations')
        .on('pageshow.exampleCollocations', function (event) {
            var page_event = event.originalEvent || event;

            if (page_event.persisted) {
                window.location.reload();
            }
        });
}

/**
 * Сохраняет оценку исходного значения из панели карточки.
 */
function rateSourceExample(el) {
    var card = $(el).closest('.example-card');
    var page = card.closest('.meaning-example-search');

    if (card.data('rating-saving')) {
        return;
    }

    card.data('rating-saving', true);
    card.find('.example-rating-button').prop('disabled', true);

    $.ajax({
        url: page.attr('data-word-block-url')
            + '/' + card.attr('data-source-word-key')
            + '/relevance',
        type: 'POST',
        dataType: 'json',
        data: {
            _token: page.attr('data-csrf-token'),
            relevance: $(el).attr('data-relevance'),
            source_w_id: card.attr('data-source-w-id'),
            fragment_id: card.attr('data-fragment-id'),
            distance_from: page.attr('data-distance-from'),
            distance_to: page.attr('data-distance-to'),
            synset_id: page.attr('data-synset-id') || ''
        },
        success: function (result) {
            updateExampleRating(card, parseInt(result.relevance, 10));

            page.find('.links-to-lemmas').hide().data('downloaded', 0);
            page.find('.example-original-content').data('downloaded', 0);
        },
        error: function () {
            alert(page.attr('data-save-error'));
        },
        complete: function () {
            card.data('rating-saving', false);
            card.find('.example-rating-button').prop('disabled', false);
        }
    });
}

/**
 * Обновляет звезду и доступность кнопок либо убирает исключённую карточку.
 */
function updateExampleRating(card, relevance) {
    if (isNaN(relevance) || relevance < 0 || relevance > 10) {
        return;
    }

    if (relevance === 0) {
        window.location.reload();
        return;
    }

    var star = card.find('.example-source-rating');

    star.empty();

    if (relevance > 1) {
        $('<span>')
            .addClass('glyphicon glyphicon-star relevance-' + relevance)
            .appendTo(star);
    }

    card.find('.example-rating-button').each(function () {
        var button_relevance = parseInt(
            $(this).attr('data-relevance'),
            10
        );

        $(this).toggle(button_relevance !== relevance);
    });
}

/**
 * Убирает карточку и пустую группу предложения.
 */
function removeExampleCard(card) {
    var group = card.closest('.example-sentence-group');
    var page = card.closest('.meaning-example-search');

    card.remove();

    if (group.length && !group.find('.example-card').length) {
        group.remove();

        var counter = page.find('.example-result-total');
        var total = parseInt(counter.text(), 10);

        if (!isNaN(total)) {
            counter.text(Math.max(0, total - 1));
        }
    }
}

/**
 * Раскрывает оригинал и загружает его вместе с готовыми разборами.
 */
/**
 * Раскрывает оригинал и загружает его вместе с готовыми разборами.
 */
function toggleExampleOriginal(el) {
    var button = $(el);
    var panel = button.closest('.example-original-panel');
    var content = panel.children('.example-original-content');
    var page = button.closest('.meaning-example-search');
    var expanded = button.attr('aria-expanded') === 'true';

    if (expanded) {
        setExampleOriginalExpanded(button, content, false);
        return;
    }

    if (content.data('downloaded')) {
        setExampleOriginalExpanded(button, content, true);
        return;
    }

    if (content.data('loading')) {
        return;
    }

    content.data('loading', true);
    button.prop('disabled', true);

    $.ajax({
        url: button.attr('data-url'),
        type: 'GET',
        data: {
            distance_from: page.attr('data-distance-from'),
            distance_to: page.attr('data-distance-to'),
            examples_return: page.attr('data-examples-return') || '',
            synset_id: page.attr('data-synset-id') || ''
        },
        success: function (html) {
            content.html(html).data('downloaded', 1);

            updateExampleCollocationMarks();

            setExampleOriginalExpanded(
                button,
                content,
                true
            );
        },
        error: function () {
            button.removeData('restore-scroll');
            alert(page.attr('data-save-error'));
        },
        complete: function () {
            content.data('loading', false);
            button.prop('disabled', false);
        }
    });
}

/**
 * Отмечает слова, чьи подтверждённые значения записаны как партнёры источника.
 */
function updateExampleCollocationMarks() {
    $('.meaning-example-search w.word-linked').each(function () {
        var word = $(this);
        var review = word.children('.links-to-lemmas')
            .children('.example-review-block')
            .first();

        if (!review.length) {
            return;
        }

        var recorded = review.attr('data-collocation-recorded') === '1';
        var mark = word.children('.example-recorded-partner');

        word.toggleClass('example-collocation-recorded', recorded);

        if (!recorded) {
            mark.remove();
            return;
        }

        if (!mark.length) {
            var page = word.closest('.meaning-example-search');
            var label = page.attr('data-collocation-recorded-label');

            $('<span>')
                .addClass('example-recorded-partner')
                .attr('title', label)
                .attr('aria-label', label)
                .text('✓')
                .appendTo(word);
        }
    });
}

/**
 * Меняет видимость оригинала и сохраняет состояние карточки в адресе.
 */
function setExampleOriginalExpanded(button, content, expanded) {
    var card = button.closest('.example-card');
    var card_id = card.attr('id');

    content.toggle(expanded);

    button.attr('aria-expanded', expanded ? 'true' : 'false')
        .text(button.attr(expanded ? 'data-hide' : 'data-show'));

    if (card_id) {
        var url = new URL(window.location.href);

        if (expanded) {
            url.searchParams.set('open_original', '1');
            url.hash = card_id;
        } else if (url.hash === '#' + card_id) {
            url.searchParams.delete('open_original');
        }

        window.history.replaceState(window.history.state, '', url.href);
    }

    if (expanded && button.data('restore-scroll')) {
        button.removeData('restore-scroll');

        if (card.length) {
            card[0].scrollIntoView({ block: 'start' });
        }
    }
}

/**
 * Восстанавливает оригинал карточки, указанной в адресе возврата.
 */
function restoreExampleOriginal() {
    var url = new URL(window.location.href);

    if (url.searchParams.get('open_original') !== '1') {
        return;
    }

    var card_id = url.hash.substring(1);

    if (!card_id) {
        return;
    }

    var card_element = document.getElementById(card_id);

    if (!card_element) {
        return;
    }

    var card = $(card_element);

    if (!card.hasClass('example-card') || !card.closest('.meaning-example-search').length) {
        return;
    }

    var button = card.find('.example-original-toggle').first();

    if (!button.length || button.attr('aria-expanded') === 'true') {
        return;
    }

    button.data('restore-scroll', true);
    toggleExampleOriginal(button[0]);
}