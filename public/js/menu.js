/*******************************************
  Changes a width of drop-down menu when the window is resized
  Called everywhere
  ******************************************/
function changeWidthDropDownMenu() {
    $(".dropdown").click(function(){
/*        if ($(this).hasClass('open')) {
            return;
        }*/
        var id = $(this).attr("id");
        var width = $(this).width();
        $("#"+id+"-sub").width(width-1);
//        $("#"+id+"-sub li a").width(width-1);
        $("#"+id+"-sub li a").css('width',width-1);
   
    });
}