@extends('layouts.page')

@section('page_title')
{{ trans('navigation.regions') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_region')}} <span class='imp'>"{{ $region->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/region/'.$region->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($region, array('method'=>'PUT', 'route' => array('region.update', $region->id))) !!}
        @include('corpus.region._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop