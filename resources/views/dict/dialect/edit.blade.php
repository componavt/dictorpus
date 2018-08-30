@extends('layouts.page')

@section('page_title')
{{ trans('navigation.dialects') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_dialect')}} <span class='imp'>"{{ $dialect->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/dialect/'.$dialect->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($dialect, array('method'=>'PUT', 'route' => array('dialect.update', $dialect->id))) !!}
        @include('dict.dialect._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop