@extends('layouts.master')

@section('title')
{{ trans('navigation.texts') }}
@stop

@section('content')
        <h1>{{ trans('navigation.texts') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_text')}} "{{ $text->title}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($text, array('method'=>'PUT', 'route' => array('text.update', $text->id))) !!}
        @include('corpus.text._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit',
                                      'lang_values' => $lang_values, 
                                      'corpus_values'  => $corpus_values])
        {!! Form::close() !!}
@stop