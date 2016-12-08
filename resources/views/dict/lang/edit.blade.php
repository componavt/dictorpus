@extends('layouts.master')

@section('title')
{{ trans('navigation.langs') }}
@stop

@section('content')
        <h1>{{ trans('navigation.langs') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_lang')}} "{{ $lang->name}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lang/'.$lang->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($lang, array('method'=>'PUT', 'route' => array('lang.update', $lang->id))) !!}
        @include('dict.lang._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop