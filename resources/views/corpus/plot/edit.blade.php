@extends('layouts.page')

@section('page_title')
{{ trans('navigation.plots') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_plot')}} <span class='imp'>"{{ $plot->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/plot/'.$plot->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($plot, array('method'=>'PUT', 'route' => array('plot.update', $plot->id))) !!}
        @include('corpus.plot._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop