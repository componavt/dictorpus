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

function addTag(el, open, close) {
	var control = $(el)[0];
	var start = control.selectionStart;
	var end = control.selectionEnd;
	if (start != end) {
		var text = $(control).val();
		$(control).val(text.substring(0, start) + open + text.substring(start, end) + close + text.substring(end));
		$(control).focus();
		var sel = end + (open + close).length;
		control.setSelectionRange(sel, sel);
	}
	return false;
}

function toSup(fieldId) {
    addTag('#'+fieldId, '<sup>', '</sup>');
}