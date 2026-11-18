/**
 * Выбирает добавление в окне либо существующее удаление сочетания.
 */
function handleExampleCollocationAction(el, event) {
    var url = new URL($(el).attr('href'), window.location.href);

    if (url.searchParams.get('edit')) {
        return deleteExampleCollocation(el, event);
    }

    return openExampleCollocation(el, event);
}

function initExampleCollocationModal() {
    var modal = $('#example-collocation-modal');

    if (!modal.length) {
        return;
    }

    modal.appendTo(document.body);
    modal.off('.exampleCollocation');

    modal.on('hide.bs.modal.exampleCollocation', function (event) {
        if (modal.data('saving')) {
            event.preventDefault();
        }
    });

    modal.on('hidden.bs.modal.exampleCollocation', function () {
        var context = modal.data('context');
        var request = modal.data('load-request');
        modal.removeData('context').removeData('load-request');

        if (request) {
            request.abort();
        }

        modal.find('.example-collocation-modal-body').empty();

        if (context) {
            var target = context.opener.is(':visible')
                ? context.opener : context.card.attr('tabindex', '-1');

            if (target.length) {
                target[0].focus({ preventScroll: true });
            }
        }
    });

    modal.on('submit.exampleCollocation', '.example-collocation-form', function (event) {
        event.preventDefault();
        saveExampleCollocation($(this), modal);
    });

    modal.on('click.exampleCollocation', '#use-dictionary-gloss', function () {
        var form = $(this).closest('form');
        form.find('[name="display_gloss"]').val(form.attr('data-dictionary-gloss') || '');
    });
}

/**
 * Берёт значения и употребления из той карточки, где нажата ссылка.
 */
function openExampleCollocation(el, event) {
    if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
        return true;
    }

    var opener = $(el);
    var card = opener.closest('.example-card');
    var page = card.closest('.meaning-example-search');
    var word = opener.closest('w');
    var modal = $('#example-collocation-modal');
    var endpoint = page.attr('data-example-collocation-url');
    var source_key = /^(\d+)_(\d+)$/.exec(card.attr('data-source-word-key') || '');
    var partner_key = /^(\d+)_(\d+)$/.exec(word.attr('data-word-key') || word.attr('id') || '');
    var url = new URL(opener.attr('href'), window.location.href);
    var partner_id = url.searchParams.get('partner_id');

    if (!modal.length || !$.fn.modal || !endpoint || !source_key || !partner_key || !partner_id) {
        return true;
    }

    event.preventDefault();
    event.stopPropagation();

    var context = { opener: opener, card: card, page: page };
    var body = modal.find('.example-collocation-modal-body');
    modal.data('context', context).data('saving', false);
    body.empty().append($('<p>').attr('role', 'status').text(modal.attr('data-loading')));
    modal.modal('show');

    var request = $.ajax({
        url: endpoint,
        type: 'GET',
        dataType: 'html',
        cache: false,
        data: {
            text_id: source_key[1],
            source_w_id: source_key[2],
            partner_w_id: partner_key[2],
            collocate_meaning_id: partner_id,
            synset_id: page.attr('data-synset-id') || ''
        }
    }).done(function (html) {
        if (modal.data('context') !== context) {
            return;
        }

        body.html(html);

        if (!body.find('.example-collocation-form').length) {
            body.empty().append($('<p>').addClass('text-danger').text(modal.attr('data-load-error')));
            return;
        }

        if (modal.hasClass('in')) {
            body.find('[name="concept_category_id"]').trigger('focus');
        } else {
            modal.one('shown.bs.modal', function () {
                if (modal.data('context') === context) {
                    body.find('[name="concept_category_id"]').trigger('focus');
                }
            });
        }
    }).fail(function (xhr, status) {
        if (status !== 'abort' && modal.data('context') === context) {
            showExampleCollocationErrors(body, xhr, modal.attr('data-load-error'));
        }
    });

    modal.data('load-request', request);
    return false;
}

function saveExampleCollocation(form, modal) {
    var context = modal.data('context');

    if (modal.data('saving') || !context) {
        return;
    }

    var data = form.serialize();
    modal.data('saving', true);
    form.find(':input').prop('disabled', true);
    form.find('.example-collocation-form-errors').empty().hide();

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        dataType: 'json',
        data: data
    }).done(function (result) {
        if (!result || !/^[1-9][0-9]*$/.test(String(result.id)) || typeof result.message !== 'string') {
            showExampleCollocationErrors(form, null, modal.attr('data-save-error'));
            return;
        }

        var notice = context.card.children('.example-collocation-notice');

        if (!notice.length) {
            notice = $('<div>').addClass('example-collocation-notice alert alert-success')
                .attr('role', 'status').prependTo(context.card);
        }

        notice.text(result.message);
        context.page.find('.links-to-lemmas').hide().data('downloaded', 0);
        context.page.find('.example-original-content').data('downloaded', 0);
        refreshExampleRecordedPartners();
        refreshExampleSentenceActions(context.card);
        modal.data('saving', false);
        modal.modal('hide');
    }).fail(function (xhr) {
        showExampleCollocationErrors(form, xhr, modal.attr('data-save-error'));
    }).always(function () {
        modal.data('saving', false);
        form.find(':input').prop('disabled', false);
    });
}

/**
 * Выводит сообщения как текст, не вставляя HTML из ответа об ошибке.
 */
function showExampleCollocationErrors(container, xhr, fallback) {
    var errors = [];
    var payload = xhr && xhr.responseJSON;

    if (xhr && xhr.status === 422 && payload) {
        $.each(payload.errors || payload, function (field, values) {
            if ($.isArray(values)) {
                $.each(values, function (index, message) {
                    if (typeof message === 'string') {
                        errors.push(message);
                    }
                });
            }
        });
    }

    if (!errors.length) {
        errors.push(fallback);
    }

    var box = container.find('.example-collocation-form-errors');

    if (!box.length) {
        container.empty();
        box = $('<div>').addClass('example-collocation-form-errors alert alert-danger')
            .attr('role', 'alert').appendTo(container);
    }

    var list = $('<ul>');
    $.each(errors, function (index, message) {
        $('<li>').text(message).appendTo(list);
    });
    box.empty().append(list).show();
}
