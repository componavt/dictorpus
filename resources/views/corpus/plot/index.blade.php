<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.plot_list') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p>
{{--            <a href="/stats/by_plot">{{ trans('stats.stats_by_plot') }}</a> | --}}
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/plot/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        | <a href="{{ LaravelLocalization::localizeURL('/corpus/topic') }}">{{ trans('navigation.topics') }}</a>
        | <a href="{{ LaravelLocalization::localizeURL('/corpus/genre') }}">{{ trans('navigation.genres') }}</a></p>
        </p>
        
        @include('corpus.plot._search_form') 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table id='plotsTable' class="table table-striped table-wide wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                @if (!$url_args['search_genre'])
                <th>{{ trans('corpus.genre') }}</th>
                @endif
                <th>{{ trans('navigation.texts') }}</th>
                <th>{{ trans('navigation.topics') }}</th>
                @if (user_corpus_edit())
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($plots as $plot)
                @include('corpus.plot._row', ['list_count' => $list_count++]) 
            @endforeach
        </tbody>
        </table>
        @endif
{{--<p><a href="/stats/by_plot">{{trans('stats.distribution_by_plots')}}</a></p>--}}
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
    
    $('#plotsTable').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.4/i18n/ru.json'
        },
        "order": [[ 0, "asc" ]]
    } );
@stop


