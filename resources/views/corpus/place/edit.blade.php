@extends('layouts.master')

@section('title')
{{ trans('navigation.places') }}
@stop

@section('content')
        <h1>{{ trans('navigation.places') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_place')}} "{{ $place->name}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/place/'.$place->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($place, array('method'=>'PUT', 'route' => array('place.update', $place->id))) !!}
        @include('corpus.place._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop