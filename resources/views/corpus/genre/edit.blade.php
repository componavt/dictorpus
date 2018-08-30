@extends('layouts.page')

@section('page_title')
{{ trans('navigation.genres') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_genre')}} <span class='imp'>"{{ $genre->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/genre/'.$genre->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($genre, array('method'=>'PUT', 'route' => array('genre.update', $genre->id))) !!}
        @include('corpus.genre._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop