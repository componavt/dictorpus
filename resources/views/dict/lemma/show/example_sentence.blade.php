<?php 
list($sxe,$error_message) = \App\Models\Corpus\Text::toXML($sentence['s'],$count);
$w = $sxe->xpath('//w[@id="'.$sentence['w_id'].'"]');
if (isset($w[0])) {
    $w[0]->addAttribute('class','word-marked');
    $sentence['s'] = $sxe->asXML();
}
?>
{!! $sentence['s'] !!}
@if ($sentence['trans_s']) 
    <br><i>{!! $sentence['trans_s'] !!}</i>
@endif
@if ($sentence['text'])
(<a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$sentence['text']->id) }}">
    {{$sentence['text']->title}}
</a>)
@endif
