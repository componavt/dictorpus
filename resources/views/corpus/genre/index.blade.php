<?php $list_count=0; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.genre_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p>
            <a href="/stats/by_genre">{{ trans('stats.stats_by_genre') }}</a> |
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/genre/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        | <a href="{{ LaravelLocalization::localizeURL('/corpus/plot') }}">{{ trans('navigation.plots') }}</a>
        </p>
        
        @include('corpus.genre._search_form') 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table table-striped table-wide wide-md">
        <thead>
            <tr>
                @if (User::checkAccess('corpus.edit'))
                <th>No</th>
                @endif
                @if (!$url_args['search_corpus'])
                <th>{{ trans('corpus.corpus') }}</th>
                @endif
                <!--th>{{ trans('corpus.parent') }}</th-->
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($genres as $genre)
                @include('corpus.genre._row') 
            @endforeach
        </tbody>
        </table>
        @endif
<p><a href="/stats/by_genre">{{trans('stats.distribution_by_genres')}}</a></p>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


