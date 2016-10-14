@extends('layouts.master')

@section('title')
{{ trans('navigation.genres') }}
@stop

@section('content')
        <h1>{{ trans('navigation.genres') }}</h1>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/genre/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('genre.store'))) !!}
        @include('corpus.genre._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop