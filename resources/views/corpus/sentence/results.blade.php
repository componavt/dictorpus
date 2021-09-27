<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.gram_search') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
        <h3>Результаты поиска</h3>
        <p>{!!$search_query!!}</p>
        @if ($refine)
            {{trans('search.refine_search')}}
        @else 
            {{trans_choice('search.founded_texts', 
                $numAll>20 ? $numAll%10 : $numAll, ['count'=>number_format($numAll, 0, ',', ' ')])}}{{trans_choice('search.founded_entries', 
                    $entry_number>20 ? ($entry_number%10 == 0 ? 5 : $entry_number%10) : $entry_number, ['count'=>number_format($entry_number, 0, ',', ' ')])}}.
            @if ($numAll)     
            <ol start='{{$list_count}}'>
                @foreach($texts as $text)
                <li>
                    @include('corpus.sentence._founded_sentences')
                </li>
                @endforeach
            </ol>
            {!! $texts->appends($url_args)->render() !!}
            @endif
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/text.js')!!}
@stop

@section('jqueryFunc')
{{-- show/hide a block with lemmas --}}
    showWordBlock(); 
@stop
