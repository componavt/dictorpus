@extends('layouts.page')

@section('page_title')
{{ trans('navigation.motives') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_motive')}} <span class='imp'>"{{ $motive->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/motive/'.$motive->id).$args_by_get }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($motive, array('method'=>'PUT', 'route' => array('motive.update', $motive->id))) !!}
        @include('corpus.motive._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    selectMotive('motype_id', 'NULL', '', true);
@stop