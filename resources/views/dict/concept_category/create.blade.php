@extends('layouts.page')

@section('page_title')
{{ trans('navigation.concept_categories') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/concept_category/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('concept_category.store'))) !!}
        @include('dict.concept_category._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop