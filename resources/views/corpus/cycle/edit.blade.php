@extends('layouts.page')

@section('page_title')
{{ trans('navigation.cycles') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_cycle')}} <span class='imp'>"{{ $cycle->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/cycle/'.$cycle->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($cycle, array('method'=>'PUT', 'route' => array('cycle.update', $cycle->id))) !!}
        @include('corpus.cycle._form_create_edit', 
                ['submit_title' => trans('messages.save'),
                 'genre_id' => $cycle->genre_id ?? $default_genre,
                 'action' => 'edit'])
        {!! Form::close() !!}
@stop