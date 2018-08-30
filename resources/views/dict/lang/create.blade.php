@extends('layouts.page')

@section('page_title')
{{ trans('navigation.langs') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lang/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('lang.store'))) !!}
        @include('dict.lang._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop