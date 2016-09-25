@extends('layouts.master')

@section('title')
{{ trans('navigation.grams') }}
@stop

@section('content')
        <h1>{{ trans('navigation.grams') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_gram')}} "{{ $gram->name}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gram/'.$gram->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($gram, array('method'=>'PUT', 'route' => array('gram.update', $gram->id))) !!}
        @include('dict.gram._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop