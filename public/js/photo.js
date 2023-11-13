function openBigPhoto(photo_block) {
   $(photo_block).on("click", function() {
        var big_photo = $(this).data('big');
        var title = $(this).data('title');
        if (title) {
            $("#modalOpenBigPhoto .modal-title")
                .html(title);
        }
        $("#modalOpenBigPhoto .modal-body")
                .html('<div class="photo-in-modal"><img src="'+big_photo
                    +'"></div>');
        $("#modalOpenBigPhoto").show();
        
    });
    $("#modalOpenBigPhoto .close, #modalOpenBigPhoto .cancel").on('click', function() {
        $("#modalOpenBigPhoto").hide();        
    });
}