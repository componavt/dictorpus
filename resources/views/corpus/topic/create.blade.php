@extends('layouts.page')

@section('page_title')
{{ trans('navigation.topics') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/topic/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('topic.store'))) !!}
        @include('corpus.topic._form_create_edit', ['action' => 'create'])
        @include('widgets.form.formitem._submit', ['title' => trans('messages.create_new_m')])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    selectPlot('.select-plot', 'genre_id');
@stop
