@extends('layouts.master')

@section('title')
{{ trans('navigation.roles') }}
@stop

@section('content')
        <h1>{{ trans('navigation.roles') }}</h1>
        <p><a href="{{ LaravelLocalization::localizeURL('/role/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('role.store'))) !!}
        @include('role._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop