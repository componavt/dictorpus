@extends('layouts.master')

@section('title')
{{ trans('navigation.gramsets') }}
@stop

@section('content')
        <h1>{{ trans('navigation.gramsets') }}</h1>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gramset/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('gramset.store'))) !!}
        @include('dict.gramset._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop