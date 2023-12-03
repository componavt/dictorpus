function limitTextarea(selector) {
    var maxCount = 2000;

    $("#counter").html(maxCount);

    $(selector).keyup(function() {
        var revText = this.value.length;

        if (this.value.length > maxCount) {
            this.value = this.value.substr(0, maxCount);
        }
        var cnt = (maxCount - revText);
        if(cnt <= 0){
            $("#counter").html('0');
        } else {
            $("#counter").html(cnt);
        }
    });    
}