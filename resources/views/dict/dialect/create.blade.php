@extends('layouts.page')

@section('page_title')
{{ trans('navigation.dialects') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/dialect/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('dialect.store'))) !!}
        @include('dict.dialect._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop
