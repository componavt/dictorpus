<?php 
/**
 * Вывод отдельного предложения-примера вместе с иконками "добавить пример", "удалить пример"
 */
//dd($sentence);
$t_s_w = $sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id'];
if (isset($meaning)) {
    $m_t_s_w = $meaning->id.'_'.$t_s_w;
} 

$place_title = $sentence['text']->dialects && isset($sentence['text']->dialects[0]) ? $sentence['text']->dialects[0]->name : '';
if($sentence['text']->event && $sentence['text']->event->place) {
        $place_title .= ', '. $sentence['text']->event->place->placeString();
}
?>
@if (isset($meaning))
    <span id="sentence-relevance_<?=$m_t_s_w;?>">
    @if (isset($relevance) && $relevance>1)
        <span class="glyphicon glyphicon-star relevance-<?=$relevance;?>"></span>
    @endif
    </span>
@endif

@include('corpus.sentence.view', ['sentence_xml' => $sentence['s'], 
                                  'text'=>$sentence['text'], 
                                  'sentence' => $sentence['sent_obj'],
                                  'marked_words'=>[$sentence['w_id']]])

@if ($sentence['trans_s']) 
    <br><i>{!! $sentence['trans_s'] !!}</i>
@endif

@if ($sentence['text'])
(<a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$sentence['text']->id. '?search_sentence='.$sentence['s_id']) }}" 
    title="{{$place_title}}">{{$sentence['text']->title}}</a>)
    @if (!empty($is_edit) && User::checkAccess('dict.edit'))
        @include('widgets.form.button._edit', 
                 ['route' => '/dict/lemma/'.$meaning->lemma->id.'/edit/example/'.$t_s_w,
                  'link_class' => 'sentence-edit',
                  'without_text' => 1])
    @endif    
    
    @if (!isset($relevance) || $relevance==1)
        @if (!empty($is_edit) && User::checkAccess('dict.edit'))
            @include('widgets.form.button._add', 
                    ['data_add' => $m_t_s_w,
                     'class' => 'add-example',
                     'relevance' => 5,
                     'title' => trans('dict.add-example-5')])
        @endif
    @endif
    @if (!isset($relevance) || $relevance!=10)
        @if (!empty($is_edit) && User::checkAccess('dict.edit'))
            @include('widgets.form.button._add', 
                    ['data_add' => $m_t_s_w,
                     'class' => 'add-great-example',
                     'relevance' => 10,
                     'title' => trans('dict.add-example-10')])
        @endif
    @endif
    @if (!empty($is_edit) && User::checkAccess('dict.edit'))
        @include('widgets.form.button._remove', 
                ['data_for' => $m_t_s_w,
                 'class' => 'remove-example',
                 'title' => trans('dict.remove-example')])
    @endif
@endif
