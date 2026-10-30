function initCollocationExampleAttach() {
    $('.meaning-example-search').on('submit', '.collocation-example-form', function (event) {
        event.preventDefault();

        var form = $(this);
        var button = form.find('button[type="submit"]');
        var error = form.find('.collocation-example-error');
        var question = form.attr('data-confirm');

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
        }).done(function () {
            var card_id = form.closest('.example-card').attr('id');
            var url = new URL(window.location.href);

            if (card_id) {
                url.hash = card_id;
                window.history.replaceState(window.history.state, '', url.href);
            }

            window.location.reload();
        }).fail(function () {
            error.text(form.attr('data-error') || $('.meaning-example-search').attr('data-attach-error'));
            button.prop('disabled', false);
        });
    });
}