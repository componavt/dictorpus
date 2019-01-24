/*******************************************
  Shows and hides the special symbol buttons 
  ******************************************/
function toggleSpecial() {
        $(".special-symbols-link").click(function(){
            var id=$(this).attr('data-for');
            $(".special-symbols").hide();
            $(".special-symbols-link").show();
            $(this).hide();
            $("#"+id).show(); /*"slow"*/
        });
}

function insertSymbol (text, fieldName) {
    var elem = document.getElementById(fieldName)
    insertTextAtCursor(elem,text);
    elem.focus();
/*    
        var textEl = $("#"+fieldName);
        oldText = textEl.val();
        textEl.val(oldText + text); */
}

function insertTextAtCursor(el, text, offset) {
    var val = el.value, endIndex, range, doc = el.ownerDocument;
    if (typeof el.selectionStart == "number"
            && typeof el.selectionEnd == "number") {
        endIndex = el.selectionEnd;
        el.value = val.slice(0, endIndex) + text + val.slice(endIndex);
        el.selectionStart = el.selectionEnd = endIndex + text.length+(offset?offset:0);
    } else if (doc.selection != "undefined" && doc.selection.createRange) {
        el.focus();
        range = doc.selection.createRange();
        range.collapse(false);
        range.text = text;
        range.select();
    }
}