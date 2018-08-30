@extends('layouts.page')

@section('page_title')
{{ trans('navigation.districts') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_district')}} <span class='imp'>"{{ $district->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/district/'.$district->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($district, array('method'=>'PUT', 'route' => array('district.update', $district->id))) !!}
        @include('corpus.district._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop