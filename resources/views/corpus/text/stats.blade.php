@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/').$args_by_get}}">{{ trans('messages.back_to_list') }}</a> |
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'/').$args_by_get }}">{{ trans('messages.back_to_show') }}</a>            
        @if (user_corpus_edit())
            | <a href="{{ route('text.concordance', $text) }}">{{ trans('navigation.concordance') }}</a>
            | <b>Аннотированный текст</b> (@foreach (range(1,3) as $type)
            <a href="/service/export/text/{{ $text->id }}/annotated/{{ $type }}">{{ $type }} вариант</a>@if ($type<3),@endif 
            @endforeach)
        @endif
        </p>
        
        <h2>{{ trans('navigation.stats') }} {{ trans('corpus.of_text') }} &laquo;{{ $text->title }}&raquo;</h2>
        <p>{{trans('stats.total_sentences')}}: <b>{{$text->sentences()->count()}}</b></p>
        
        <p>
            {{trans('stats.total_words_in_text')}}: <b>{{$totalWords}}</b>, {{trans('stats.of_them')}}
            <br><span style='margin-left:42px;'>{{trans('stats.marked_words_in_text')}}: <b>{{$markedWords}}</b> ({{$markedWordsToAll}}%), {{trans('stats.of_them')}}</span>
            <br><span style='margin-left:42px;'>{{trans('stats.checked_words_in_text')}}: <b>{{$checked_words}}</b> ({{$checkedWordsToMarked}}%)</span>
        </p>

        <p>
            {{trans('stats.total_lemmas')}}: <b>{{$totalLemmas}}</b>, {{trans('stats.of_them')}}
            @foreach ($lemmas_by_pos as $name=>$info)
                <br><span style='margin-left:42px;'>{{$name}}: <b>{{$info['count']}}</b> ({{$info['%']}}%)</span>
            @endforeach        
        <p style='font-style: italic; text-align: right'>{{date('d-m-Y')}}</p>
        
        
@stop
