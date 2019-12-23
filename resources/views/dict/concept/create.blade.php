@extends('layouts.page')

@section('page_title')
{{ trans('navigation.concepts') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/concept/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('concept.store'))) !!}
        @include('dict.concept._form_create_edit', 
                ['submit_title' => trans('messages.create_new_m'),
                 'concept_category_id' => NULL, 
                 'pos_id' => NULL,
                 'action' => 'create'])
        {!! Form::close() !!}
@stop