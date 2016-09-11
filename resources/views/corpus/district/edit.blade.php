@extends('layouts.master')

@section('title')
{{ trans('navigation.districts') }}
@stop

@section('content')
        <h1>{{ trans('navigation.districts') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_district')}} "{{ $district->name}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/district/'.$district->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($district, array('method'=>'PUT', 'route' => array('district.update', $district->id))) !!}
        @include('corpus.district._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop