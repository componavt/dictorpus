@extends('layouts.page')

@section('page_title')
{{ trans('navigation.topics') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_topic')}} <span class='imp'>"{{ $topic->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/topic/'.$topic->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($topic, array('method'=>'PUT', 'route' => array('topic.update', $topic->id))) !!}
        @include('corpus.topic._form_create_edit', ['action' => 'edit'])
        @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    selectPlot('.select-plot', 'genre_id');
@stop
