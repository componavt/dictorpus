<?php $list_count = 1;?>
@extends('layouts.master')

@section('title')
{{ trans('corpus.genre_list') }}
@stop

@section('content')
        <h2>{{ trans('corpus.genre_list') }}</h2>
        
        <p style="text-align:right">
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
        @include('widgets.form._formitem_text', 
                ['name' => 'search_id', 
                'value' => $search_id,
                'attributes'=>['size' => 3,
                               'placeholder' => 'ID']])
         @include('widgets.form._formitem_text', 
                ['name' => 'genre_name', 
                'value' => $genre_name,
                'attributes'=>['size' => 15,
                               'placeholder' => trans('corpus.name')]])
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th colspan="2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($genres as $genre)
            <tr>
                <td>{{ $list_count++ }}</td>
                <td>{{$genre->name_en}}</td>
                <td>{{$genre->name_ru}}</td>
                <td>
                    @if($genre->texts)
                        {{ $genre->texts()->count() }}
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td>
                    @include('widgets.form._button_edit', ['is_button'=>true, 'route' => '/corpus/genre/'.$genre->id.'/edit'])
                 </td>
                <td>
                    @include('widgets.form._button_delete', ['is_button'=>true, $route = 'genre.destroy', 'id' => $genre->id])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/genre');
@stop


