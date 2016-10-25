<?php $list_count = 1;?>
@extends('layouts.master')

@section('title')
{{ trans('corpus.recorder_list') }}
@stop

@section('content')
        <h2>{{ trans('corpus.recorder_list') }}</h2>
        
        <p style="text-align:right">
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/recorder/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/corpus/recorder/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_text', 
                ['name' => 'search_id', 
                'value' => $search_id,
                'attributes'=>['size' => 3,
                               'placeholder' => 'ID']])
         @include('widgets.form._formitem_text', 
                ['name' => 'recorder_name', 
                'value' => $recorder_name,
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
                @if (User::checkAccess('corpus.edit'))
                <th colspan="2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($recorders as $recorder)
            <tr>
                <td>{{ $list_count++ }}</td>
                <td>{{$recorder->name_en}}</td>
                <td>{{$recorder->name_ru}}</td>
                @if (User::checkAccess('corpus.edit'))
                <td>
                    @include('widgets.form._button_edit', ['is_button'=>true, 'route' => '/corpus/recorder/'.$recorder->id.'/edit'])
                 </td>
                <td>
                    @include('widgets.form._button_delete', ['is_button'=>true, $route = 'recorder.destroy', 'id' => $recorder->id])
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
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/recorder');
@stop


