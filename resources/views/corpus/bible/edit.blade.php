@extends('layouts.page')

@section('page_title')
{{ trans('navigation.bibles') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_bible')}} <span class='imp'>"{{ $bible->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/bible/'.$bible->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($bible, array('method'=>'PUT', 'route' => array('bible.update', $bible->id))) !!}
        @include('corpus.bible._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop