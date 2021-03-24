<?php $list_count = 1;?>
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
        </p>
        
        {!! Form::open(['url' => '/corpus/genre/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $url_args['search_id'],
                'attributes'=>['size' => 3,
                               'placeholder' => 'ID']])
         @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                'value' => $url_args['search_name'],
                'attributes'=>['size' => 15,
                               'placeholder' => trans('corpus.name')]])
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
        {!! Form::close() !!}

        @include('widgets.founded_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table table-striped table-wide wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('corpus.corpus') }}</th>
                <th>{{ trans('corpus.parent') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($genres as $genre)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('corpus.corpus') }}">{{$genre->corpus->name ?? ''}}</td>
                <td data-th="{{ trans('corpus.parent') }}">{{$genre->parent->name ?? ''}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$genre->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$genre->name_ru}}</td>
                <td data-th="{{ trans('navigation.texts') }}">
                @if($genre->texts)
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_genre[]='.$genre->id) }}">{{ $genre->texts()->count() }}</a>
                @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/genre/'.$genre->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'genre.destroy', 
                             'args'=>['id' => $genre->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


