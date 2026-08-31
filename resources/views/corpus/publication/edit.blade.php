@extends('layouts.page')

@section('page_title')
{{ trans('navigation.source_publications') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_publication')}} <span class='imp'>"{{ $publication->title}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/publication/'.$publication->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($publication, [
            'method'=>'PUT', 
            'route' => ['publication.update', $publication->id],
            'files' => true
            ]) !!}

        @include('corpus.publication._form_create_edit', [
                'action' => 'edit',
                'with_photo' => true])

        @include('widgets.form.formitem._submit', [
            'title' => trans('messages.save')])
            
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!! js('publication')!!}
@stop

@section('jqueryFunc')
    initPublicationPubparts();
    initPublicationPeriodicFields();
@stop
