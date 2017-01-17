@extends('layouts.master')

@section('title')
{{ trans('navigation.gramsets') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.gramsets') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_gramset')}} "{{ $gramset->gramsetString()}}"</h2>
        <!--p><a href="{{ LaravelLocalization::localizeURL('/dict/gramset/'.$gramset->id) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a></p-->
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gramset/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::model($gramset, array('method'=>'PUT', 'route' => array('gramset.update', $gramset->id))) !!}
        @include('dict.gramset._form_create_edit', ['submit_title' => trans('messages.save'),
                                                    'action' => 'edit',
                                                    'pos_value' => $pos_value])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
@stop

@section('jqueryFunc')
    $(".multiple-select").select2();
@stop