@extends('layouts.page')

@section('page_title')
{{ trans('navigation.source_publications') }}
@stop

@section('body')
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/publication/') }}">{{ trans('messages.back_to_list') }}</a></p>
    
    {!! Form::open(array('method'=>'POST', 'route' => array('publication.store'))) !!}
    @include('corpus.publication._form_create_edit', ['submit_title' => trans('messages.create'),
                                    'publication' => null,
                                    'action' => 'create'])
    @include('widgets.form.formitem._submit', ['title' =>  trans('messages.create_new_f')])
    {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!! js('publication')!!}
@stop

@section('jqueryFunc')
    initPublicationPeriodicFields();
@stop
