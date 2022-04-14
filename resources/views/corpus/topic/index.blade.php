<?php $list_count=0; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.topic_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p>
        @if (user_corpus_edit())
            <a href="{{ LaravelLocalization::localizeURL('/corpus/topic/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (user_corpus_edit())
            </a>
        @endif
        </p>
        
        @include('corpus.topic._search_form') 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table table-striped table-wide wide-md">
        <thead>
            <tr>
                @if (User::checkAccess('corpus.edit'))
                <th>No</th>
                @endif
                @if (!$url_args['search_plot'])
                <th>{{ trans('corpus.plot') }}</th>
                @endif
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                @if (user_corpus_edit())
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($topics as $topic)
                @include('corpus.topic._row') 
            @endforeach
        </tbody>
        </table>
        {!! $topics->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    $(".multiple-select-corpus").select2();
    selectGenre();
    selectPlot();
@stop


