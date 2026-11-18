/**
 * Прикрепляет и открепляет пример без перезагрузки страницы.
 */
function initCollocationExampleAttach() {
    $('.meaning-example-search')
        .off('submit.collocationExamples', '.collocation-example-form')
        .on(
            'submit.collocationExamples',
            '.collocation-example-form',
            function (event) {
                event.preventDefault();
                submitCollocationExample($(this));
            }
        );
}

/**
 * Отправляет форму и проверяет ответ сервера.
 */
function submitCollocationExample(form) {
    var button = form.find('button[type="submit"]');
    var error = form.find('.collocation-example-error');
    var question = form.attr('data-confirm');
    var detaching = form.hasClass('collocation-example-detach');

    if (button.prop('disabled')) {
        return;
    }

    if (question && !window.confirm(question)) {
        return;
    }

    button.prop('disabled', true);
    error.text('');

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        dataType: 'json'
    }).done(function (result) {
        if (detaching && result && result.detached === true) {
            setCollocationExampleState(form, false, null);
            return;
        }

        if (
            !detaching
            && result
            && /^[1-9][0-9]*$/.test(String(result.id))
        ) {
            setCollocationExampleState(form, true, result.id);
            return;
        }

        error.text(form.attr('data-error'));
    }).fail(function () {
        error.text(form.attr('data-error'));
    }).always(function () {
        button.prop('disabled', false);
    });
}

/**
 * Переключает форму между прикреплением и откреплением.
 */
function setCollocationExampleState(form, attached, attachment_id) {
    var block = form.closest('.collocation-example-action');
    var examples_url = block.attr('data-examples-url');

    form
        .toggleClass('collocation-example-detach', attached)
        .attr(
            'action',
            attached ? examples_url + '/' + attachment_id : examples_url
        )
        .attr(
            'data-confirm',
            attached ? block.attr('data-detach-confirm') : ''
        )
        .attr(
            'data-error',
            block.attr(attached ? 'data-detach-error' : 'data-attach-error')
        );

    form.find('input[name="_method"]').prop('disabled', !attached);

    form.find('button[type="submit"]').text(
        block.attr(attached ? 'data-detach-label' : 'data-attach-label')
    );

    block.find('.collocation-example-status').toggle(attached);
}