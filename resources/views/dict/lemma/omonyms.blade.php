<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('navigation.omonyms') }}
@stop

@section('content')
        <h2>{{ trans('navigation.omonyms') }}</h2>

        @include('dict.lemma.search._omonyms_form',['url' => '/dict/lemma/omonyms']) 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($lemmas)
        <table class="table-bordered table-wide table-striped">
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
                <td>{{ $list_count++ }}</td>
                <td>{{ $lemma->lang->name }}</td>
                <td>{{ $lemma->pos->name }}</td>
                <td><a href="/dict/lemma/{{$lemma->id}}{{$args_by_get}}">{{$lemma->lemma}}</a></td>
                <td>
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