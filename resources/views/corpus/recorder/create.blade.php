@extends('layouts.master')

@section('title')
{{ trans('navigation.recorders') }}
@stop

@section('content')
        <h1>{{ trans('navigation.recorders') }}</h1>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/recorder/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('recorder.store'))) !!}
        @include('corpus.recorder._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop