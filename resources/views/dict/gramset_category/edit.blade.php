@extends('layouts.master')

@section('title')
{{ trans('navigation.gramset_categories') }}
@stop

@section('content')
        <h1>{{ trans('navigation.grams') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_category')}} <span class='imp'>"{{ $gramset_category->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gramset_category/'.$gramset_category->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($gramset_category, array('method'=>'PUT', 'route' => array('gramset_category.update', $gramset_category->id))) !!}
        @include('dict.gramset_category._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop