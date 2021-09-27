<?php
if (isset($with_edit) && $with_edit) {
    $sentence_xml = $text->setLemmaLink($sentence_xml, null, null, true, $marked_words); 
} elseif (isset($for_view) && $for_view) {
    $sentence_xml = $sentence_obj->addWordBlocks($marked_words); 
} else {
    list($sxe,$error_message) = \App\Models\Corpus\Text::toXML($sentence_xml,$count);
    if (!$sxe) {
        print $error_message;
    } else {
        foreach ($marked_words as $marked_word) {
            $w = $sxe->xpath('//w[@id="'.$marked_word.'"]');
            if (isset($w[0])) {
                $w[0]->addAttribute('class','word-marked');
                $sentence_xml = $sxe->asXML();
            }
        }
    }
}
$sentence_xml = mb_ereg_replace('[¦^]', '', $sentence_xml);
?>
{!! $sentence_xml !!}

