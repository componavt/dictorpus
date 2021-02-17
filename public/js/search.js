/*******************************************
  Shows and hides the search form 
  ******************************************/
function toggleSearchForm() {
        $(".show-search-form").click(function(){
            $(".show-search-form").hide();
/*            $(".search-button-b").css('padding-top', 0);*/
            $(".ext-form").show("slow");
            /*css('display', 'table');*/
            $(".hide-search-form").show();
        });
        $(".hide-search-form").click(function(){
            $(".hide-search-form").hide();
            $(".ext-form").hide("slow");
/*            $(".search-button-b").css('padding-top', '25px');*/
            $(".show-search-form").show();
        });
}
