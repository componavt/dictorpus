<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.recorder_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
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

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table-bordered table-striped table-wide rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($recorders as $recorder)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$recorder->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$recorder->name_ru}}</td>
                <td data-th="{{ trans('navigation.texts') }}">
                   @if($recorder->texts())
                   <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}{{$args_by_get ? $args_by_get.'&' : '?'}}search_recorder={{$recorder->id}}">
                       {{ $recorder->texts()->count() }} 
                   </a>
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/recorder/'.$recorder->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'recorder.destroy', 
                             'args'=>['id' => $recorder->id]])
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
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


