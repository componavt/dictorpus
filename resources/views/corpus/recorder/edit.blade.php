@extends('layouts.page')

@section('page_title')
{{ trans('navigation.recorders') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_recorder')}} <span class='imp'>"{{ $recorder->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/recorder/'.$recorder->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($recorder, array('method'=>'PUT', 'route' => array('recorder.update', $recorder->id))) !!}
        @include('corpus.recorder._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop