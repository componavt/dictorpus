(function (name, definition){
    if (typeof define === 'function') {
        define(definition);
    } else if (typeof module !== 'undefined' && module.exports) {
        module.exports = definition();
    } else {
        var theModule = definition(), global = this, old = global[name];
        theModule.noConflict = function () {
            global[name] = old;
            return theModule;
        };
        global[name] = theModule;
    }
})('ConfirmationWindow', function() {
    var ConfirmationWindow = (function() {        
	var deferred, $modal;
    
        var show = function() {
            $modal.addClass('active');

            $modal.on('click', '.confirm_cancel', decline);
            $modal.on('click', '.confirm_ok', confirm);
        };

        var close = function() {
            $modal.removeClass('active');

            $modal.off('click', '.confirm_cancel', decline);
            $modal.off('click', '.confirm_ok', confirm);
        };

        var decline = function(event) {
            event.preventDefault();
            deferred.reject();
            close();
        };
        var confirm = function(event) {
            event.preventDefault();
            deferred.resolve();
            close();
        };        

        return {
            /**
             * Запрос подтверждения
             */
            requestConfirmation: function(modal) {
                // запоминаем ссылку на модальное окно
                $modal = modal;
                // создаем deferred объект
                deferred = new $.Deferred();
                // отображаем модальное окно
                show();
                // возвращаем deferred объект
                return deferred.promise();
            }
        };
    })();

    return ConfirmationWindow;
});        