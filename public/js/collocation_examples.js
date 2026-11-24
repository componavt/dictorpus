/**
 * Сохраняет решения по примерам без перезагрузки страницы.
 */
function initCollocationExampleAttach() {
    initCollocationExampleReviewFilter();

    $('.meaning-example-search')
        .off('submit.collocationExamples', '.collocation-example-form')
        .on('submit.collocationExamples', '.collocation-example-form', function (event) {
            event.preventDefault();
            submitCollocationExample($(this));
        });
}

function submitCollocationExample(form) {
    var block = form.closest('.collocation-example-action');
    var error = form.find('.collocation-example-error');
    var decision = form.attr('data-decision');
    var question = form.attr('data-confirm');

    var blocks = $('.meaning-example-search .collocation-example-action').filter(function () {
        var candidate = $(this);

        return candidate.attr('data-collocation-id') === block.attr('data-collocation-id')
            && candidate.attr('data-text-id') === block.attr('data-text-id')
            && candidate.attr('data-source-w-id') === block.attr('data-source-w-id')
            && candidate.attr('data-partner-w-id') === block.attr('data-partner-w-id');
    });

    if (blocks.filter(function () { return $(this).data('decisionBusy'); }).length) {
        return;
    }

    if (question && !window.confirm(question)) {
        return;
    }

    blocks.data('decisionBusy', true);
    blocks.find('button[type="submit"]').prop('disabled', true);
    blocks.find('.collocation-example-error').text('');

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        dataType: 'json'
    }).done(function (result) {
        if (decision === 'reset' && result && result.detached === true) {
            blocks.each(function () {
                setCollocationExampleState($(this), null, null);
            });
            return;
        }

        if (
            decision !== 'reset'
            && result
            && result.status === decision
            && /^[1-9][0-9]*$/.test(String(result.id))
        ) {
            blocks.each(function () {
                setCollocationExampleState($(this), result.status, result.id);
            });
            return;
        }

        error.text(form.attr('data-error'));
    }).fail(function () {
        error.text(form.attr('data-error'));
    }).always(function () {
        blocks.data('decisionBusy', false);
        blocks.find('button[type="submit"]').prop('disabled', false);
    });
}

function setCollocationExampleState(block, status, decision_id) {
    var examples_url = block.attr('data-examples-url');
    var status_key = status || 'unreviewed';

    block.find('.collocation-example-status')
        .text(block.attr('data-' + status_key + '-label'))
        .toggleClass('text-success', status === 'attached')
        .toggleClass('text-muted', status === 'rejected');

    block.find('.collocation-example-form').each(function () {
        var form = $(this);
        var decision = form.attr('data-decision');
        var is_reset = decision === 'reset';
        var visible = is_reset ? decision_id !== null : status !== decision;

        form.attr('action', is_reset ? examples_url + '/' + decision_id : examples_url);
        form.css('display', visible ? 'inline-block' : 'none');
        form.find('.collocation-example-error').text('');
    });
}

function initCollocationExampleReviewFilter() {
    var section = $('#example-review-filter');
    var status = section.find('select[name="example_status"]');
    var partner = section.closest('form').find('[name="partner_meaning_id"]');

    if (!section.length || !partner.length) {
        return;
    }

    function updateReviewFilter() {
        var has_partner = !!partner.val();

        if (!has_partner) {
            status.val('all');
        }

        status.prop('disabled', !has_partner);
        section.toggle(has_partner);
    }

    partner.off('change.collocationReviewFilter')
        .on('change.collocationReviewFilter', updateReviewFilter);

    updateReviewFilter();
}