function initMeaningExampleFilters() {
    $('.example-extra-select').each(function () {
        var select = $(this);

        select.select2({
            width: '100%',
            placeholder: select.attr('data-placeholder')
        });
    });

    initExampleCombinationConditions();
}

function initExampleCombinationConditions() {
    $('.example-combination').each(function () {
        var block = $(this);
        var whole = block.find('input[type="checkbox"][name="whole_sentence"]');

        function update() {
            var disabled = whole.prop('checked');

            block.find('.example-distance-range input[type="number"]').each(function () {
                var input = $(this);
                var copy = block.find('.example-distance-copy').filter(function () {
                    return this.name === input.attr('name');
                });

                copy.val(input.val()).prop('disabled', !disabled);
                input.prop('disabled', disabled);
            });

            block.find('.example-distance-fields').toggleClass('is-disabled', disabled);
        }

        whole.on('change', update);
        block.find('.example-distance-range input[type="number"]').on('input change', update);
        update();
    });
}