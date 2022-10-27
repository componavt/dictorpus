@extends('layouts.page')

@section('page_title')
{{ trans('navigation.motypes') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/motype/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('motype.store'))) !!}
        @include('corpus.motype._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop