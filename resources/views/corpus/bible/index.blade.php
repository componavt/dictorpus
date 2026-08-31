<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.bible_list') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p>
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/bible/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        @include('corpus.bible._search_form') 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table id='biblesTable' class="table table-striped table-wide wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                @if (user_corpus_edit())
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($bibles as $bible)
                @include('corpus.bible._row', ['list_count' => $list_count++]) 
            @endforeach
        </tbody>
        </table>
        @endif
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.4/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/sorting/numeric-comma.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/type-detection/numeric-comma.js"></script>
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    $(".multiple-select-corpus").select2();
    selectGenre();
    
    $('#biblesTable').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.4/i18n/ru.json'
        },
        "order": [[ 0, "asc" ]]
    } );
@stop


