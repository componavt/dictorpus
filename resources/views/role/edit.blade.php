@extends('layouts.page')

@section('page_title')
{{ trans('navigation.roles') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('auth.of_role')}} <span class='imp'>"{{ $role->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/role/'.$role->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($role, array('method'=>'PUT', 'route' => array('role.update', $role->id))) !!}
        @include('role._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop