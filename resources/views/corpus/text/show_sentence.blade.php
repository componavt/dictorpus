<?php 
list($sxe,$error_message) = \App\Models\Corpus\Text::toXML($sentence['s'],$count);
foreach ($sentence['w_id'] as $w_id) {
    $w = $sxe->xpath('//w[@id="'.$w_id.'"]');
    if (isset($w[0])) {
        $w[0]->addAttribute('class','word-marked');
        $sentence['s'] = $sxe->asXML();
    }
}
?>
{!! $sentence['s'] !!}
