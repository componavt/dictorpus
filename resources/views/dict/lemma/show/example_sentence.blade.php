<?php 
//dd($sentence);
$t_s_w = $sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id'];
if (isset($meaning)) {
    $m_t_s_w = $meaning->id.'_'.$t_s_w;
} 

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
@if (isset($meaning))
    <span id="sentence-relevance_<?=$m_t_s_w;?>">
    @if (isset($relevance) && $relevance>1)
        <span class="glyphicon glyphicon-star relevance-<?=$relevance;?>"></span>
    @endif
    </span>
@endif

{!! $sentence['s'] !!}
@if ($sentence['trans_s']) 
    <br><i>{!! $sentence['trans_s'] !!}</i>
@endif
@if ($sentence['text'])
(<a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$sentence['text']->id) }}" 
    title="{{$place_title}}">
    {{$sentence['text']->title}}</a>)
    @if (isset($is_edit) && User::checkAccess('dict.edit'))
        @include('widgets.form.button._edit', 
                 ['route' => '/dict/lemma/'.$meaning->lemma->id.'/edit/example/'.$t_s_w,
                  'link_class' => 'sentence-edit',
                  'without_text' => 1])
    @endif    
    
    @if (!isset($relevance) || $relevance==1)
        @if (isset($is_edit) && User::checkAccess('dict.edit'))
            @include('widgets.form.button._add', 
                    ['data_add' => $m_t_s_w,
                     'class' => 'add-example',
                     'title' => trans('dict.add-example-5')])
        @endif
    @endif
    @if (isset($is_edit) && User::checkAccess('dict.edit'))
        @include('widgets.form.button._remove', 
                ['data_for' => $m_t_s_w,
                 'class' => 'remove-example',
                 'title' => trans('dict.remove-example')])
    @endif
@endif
