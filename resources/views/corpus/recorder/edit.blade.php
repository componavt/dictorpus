@extends('layouts.master')

@section('title')
{{ trans('navigation.recorders') }}
@stop

@section('content')
        <h1>{{ trans('navigation.recorders') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_recorder')}} "{{ $recorder->name}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/recorder/'.$recorder->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($recorder, array('method'=>'PUT', 'route' => array('recorder.update', $recorder->id))) !!}
        @include('corpus.recorder._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop