@extends('layouts.page')

@section('page_title')
{{ trans('navigation.grams') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gram/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('gram.store'))) !!}
        @include('dict.gram._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop
