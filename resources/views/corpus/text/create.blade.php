@extends('layouts.master')

@section('title')
{{ trans('navigation.texts') }}
@stop

@section('content')
        <h1>{{ trans('navigation.texts') }}</h1>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('text.store'))) !!}
        @include('corpus.text._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create',
                                      'lang_values' => $lang_values, 
                                      'corpus_values'  => $corpus_values])
        {!! Form::close() !!}
@stop