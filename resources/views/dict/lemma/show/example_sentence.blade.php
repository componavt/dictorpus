<?php 
//dd($sentence);
if($sentence['text']->event && $sentence['text']->event->place) {
        $place_title = $sentence['text']->event->place->placeString();
} else { 
    $place_title =''; 
}
list($sxe,$error_message) = \App\Models\Corpus\Text::toXML($sentence['s'],$count);
$w = $sxe->xpath('//w[@id="'.$sentence['w_id'].'"]');
if (isset($w[0])) {
    $w[0]->addAttribute('class','word-marked');
    $sentence['s'] = $sxe->asXML();
}
?>
@if (isset($relevance) && $relevance>1)
    <span class="glyphicon glyphicon-star relevance-<?=$relevance;?>"></span>
@endif

{!! $sentence['s'] !!}
@if ($sentence['trans_s']) 
    <br><i>{!! $sentence['trans_s'] !!}</i>
@endif
@if ($sentence['text'])
(<a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$sentence['text']->id) }}" 
    title="{{$place_title}}">
    {{$sentence['text']->title}}</a>)
@endif
