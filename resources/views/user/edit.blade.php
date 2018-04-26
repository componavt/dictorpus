@extends('layouts.master')

@section('title')
{{ trans('navigation.users') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.users') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('auth.of_user')}} "{{ $user->name}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/user/'.$user->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($user, array('method'=>'PUT', 'route' => array('user.update', $user->id))) !!}
        @include('user._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
@stop

@section('jqueryFunc')
    $(".multiple-select").select2();
@stop
