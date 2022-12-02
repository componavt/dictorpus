<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.gram_search') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
        <h3>{{trans('search.search_results')}}</h3>
        <p>{!!$search_query!!}</p>
        <p>{{trans_choice('search.found_texts', 
            $numAll>20 ? ($numAll%10 == 0 ? 5 : $numAll%10) : $numAll, ['count'=>number_format($numAll, 0, ',', ' ')])}}{{trans_choice('search.found_entries', 
            $entry_number>20 ? ($entry_number%10 == 0 ? 5 : $entry_number%10) : $entry_number, ['count'=>number_format($entry_number, 0, ',', ' ')])}}.</p>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/sentence') }}{{$args_by_get}}">{{trans('search.refine_search')}}</a></p>
        @if (!$refine)
            @if ($numAll)     
            <ol start='{{$list_count}}'>
                @foreach($texts as $text)
                <li>
                    @include('corpus.sentence._found_sentences', 
                    ['sentences' => $text->getSentencesByIds($text_sentences[$text->id]),
                     'words' => $text->getWords($url_args['words'])])
                </li>
                @endforeach
            </ol>
            {!! $texts->appends($url_args)->render() !!}
            @endif
        @endif
        
        <p>{{trans('messages.script_executed', ['n'=>(int)(microtime(true) - $script_start)])}}</p>
@stop

@section('footScriptExtra')
    {!!Html::script('js/text.js')!!}
@stop

@section('jqueryFunc')
{{-- show/hide a block with lemmas --}}
    showWordBlock('{{LaravelLocalization::getCurrentLocale()}}'); 
@stop
