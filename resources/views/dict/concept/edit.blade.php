@extends('layouts.page')

@section('page_title')
{{ trans('navigation.concepts') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_concept')}} <span class='imp'>"{{ $concept->text}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/concept/'.$concept->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($concept, array('method'=>'PUT', 'route' => array('concept.update', $concept->id))) !!}
        @include('dict.concept._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop