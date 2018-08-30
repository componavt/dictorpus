@extends('layouts.page')

@section('page_title')
{{ trans('navigation.informants') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_informant')}} <span class='imp'>"{{ $informant->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/informant/'.$informant->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($informant, array('method'=>'PUT', 'route' => array('informant.update', $informant->id))) !!}
        @include('corpus.informant._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit',
                                      'place_values' => $place_values])
        {!! Form::close() !!}
@stop