/**
 * Поля синсета предназначены только для форматированного текста.
 */
(function () {
    function isSynsetEditor(element) {
        return element
            && element.matches
            && element.matches(
                'trix-editor[data-synset-text-field]'
            );
    }

    document.addEventListener('trix-file-accept', function (event) {
        if (isSynsetEditor(event.target)) {
            event.preventDefault();
        }
    });

    document.addEventListener('trix-attachment-add', function (event) {
        if (isSynsetEditor(event.target)) {
            event.attachment.remove();
        }
    });
})();