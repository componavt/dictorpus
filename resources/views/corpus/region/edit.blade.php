@extends('layouts.master')

@section('title')
{{ trans('navigation.regions') }}
@stop

@section('content')
        <h1>{{ trans('navigation.regions') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_region')}} "{{ $region->name}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/region/'.$region->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($region, array('method'=>'PUT', 'route' => array('region.update', $region->id))) !!}
        @include('corpus.region._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop