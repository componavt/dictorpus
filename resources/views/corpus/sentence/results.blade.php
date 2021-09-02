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
        {{trans_choice('corpus.founded_texts', $numAll>20 ? $numAll%10 : $numAll, ['count'=>$numAll])}}
        @if ($numAll)     
        <ol start='{{$list_count}}'>
            @foreach($texts as $text)
            <li>
                @include('corpus.sentence._text_link')
                @include('corpus.sentence._founded_sentences')
            </li>
            @endforeach
        </ol>
        {!! $texts->appends($url_args)->render() !!}
        @endif
@stop
