@extends('layouts.page')

@section('page_title')
{{ trans('navigation.concept_categories') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_category')}} <span class='imp'>"{{ $concept_category->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/concept_category/'.$concept_category->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($concept_category, array('method'=>'PUT', 'route' => array('concept_category.update', $concept_category->id))) !!}
        @include('dict.concept_category._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop