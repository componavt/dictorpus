@extends('layouts.master')

@section('title')
{{ trans('navigation.corpuses') }}
@stop

@section('content')
        <h1>{{ trans('navigation.corpuses') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_corpus')}} "{{ $corpus->name}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/corpus/'.$corpus->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($corpus, array('method'=>'PUT', 'route' => array('corpus.update', $corpus->id))) !!}
        @include('corpus.corpus._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop