@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('content')
        <h1>{{ trans('navigation.lemmas') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_lemma')}} "{{ $lemma->lemma}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($lemma, array('method'=>'PUT', 'route' => array('lemma.update', $lemma->id))) !!}
        @include('dict.lemma._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit',
                                      'lang_values' => $lang_values, 
                                      'pos_values'  => $pos_values])
        {!! Form::close() !!}
@stop