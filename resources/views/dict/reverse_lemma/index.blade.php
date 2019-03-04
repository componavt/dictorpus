<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.reverse_dictionary') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        @include('dict.reverse_lemma._search_form',['url' => '/dict/reverse_lemma/']) 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($numAll)
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th style="text-align: right">{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.pos') }}</th>
            </tr>
        </thead>
            @foreach($reverse_lemmas as $reverse_lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lemma') }}" class="big-size to-right">
                    @if ($reverse_lemma && $reverse_lemma->lemma)
                    <a href="lemma/{{$reverse_lemma->id}}{{$args_by_get}}">
                        {{$reverse_lemma->stem}}<b>{{$reverse_lemma->affix}}</b></a>
                    @endif
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                    @if($reverse_lemma->lemma->pos)
                        <span title="{{$reverse_lemma->lemma->pos->name}}">{{$reverse_lemma->lemma->pos->code}}</span>
                        @if ($reverse_lemma->lemma->reflexive)
                            ({{ trans('dict.reflexive') }})
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
            {!! $reverse_lemmas->appends($url_args)->render() !!}
            
            <p><big>*</big> -  {{ trans('dict.example_comment') }}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop


