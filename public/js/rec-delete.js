function recDelete(confirm_message,url) {
    $('.form-delete').on('click', function(e){
        e.preventDefault();
        var $form=$(this);
        $('#confirm').modal({ backdrop: 'static', keyboard: false })
            .on('click', '#delete-btn', function(){
                $form.submit();
            });
    });    
/*    $('.delete-link').on('click',function(e){
        if(!confirm(confirm_message)){
              e.preventDefault();
        } else {
            var id = $(this).val();

            $.ajax({
                type: "DELETE",
                url: url + '/' + id,
                dataType: 'json',
                success: function (data) {
                    alert('success:'+data);
                    console.log(data);
                    $("#row" + id).remove();
                },
                error: function (data) {
                    alert('error: '+data);
                    console.log('Error:', data);
                }
            });            
        }
    });
*/
/*
    $('.delete-link').on('submit',function(e){
        
        if(!confirm(confirm_message)){
              e.preventDefault();
        } 
    });
*/

    /*
    var url = "/dict/lemma";
    //delete task and remove it from list
    $('.delete-but').click(function(){
        var id = $(this).val();

        $.ajax({

            type: "DELETE",
            url: url + '/' + task_id,
            success: function (data) {
                console.log(data);
                $("#task" + task_id).remove();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });*/
    
 /*   (function (name, definition){
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
                // Запрос подтверждения
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

    $('.delete-link').on('click', function(event) {
        event.preventDefault();

        $.when(ConfirmationWindow.requestConfirmation($('#delete_confirm')))
        .then(function() {
            console.log('resolved');
            alert('resolved')
        }, function() {
            console.log('rejected');
            alert('rejected')
        });
    });    
    */
}
