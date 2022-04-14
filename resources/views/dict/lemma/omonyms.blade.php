<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.omonyms') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        @include('dict.lemma.search._omonyms_form',['url' => '/dict/lemma/omonyms']) 

        @include('widgets.found_records', ['numAll'=>$numAll])

        @if ($lemmas)
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.interpretation') }}</th>
            </tr>
        </thead>
            @foreach($lemmas as $lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lang') }}">{{ $lemma->lang->name }}</td>
                <td data-th="{{ trans('dict.pos') }}">{{ $lemma->pos->name }}</td>
                <td data-th="{{ trans('dict.lemma') }}"><a href="/dict/lemma/{{$lemma->id}}{{$args_by_get}}">{{$lemma->lemma}}</a></td>
                <td data-th="{{ trans('dict.interpretation') }}">
                    @foreach ($lemma->meanings as $meaning_obj) 
                        {{$meaning_obj->getMultilangMeaningTextsString(LaravelLocalization::getCurrentLocale())}}<br>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </table>
            {!! $lemmas->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop