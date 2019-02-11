@extends('layouts.page')

@section('page_title')
{{ trans('navigation.gramset_categories') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gramset_category/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('gramset_category.store'))) !!}
        @include('dict.gramset_category._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop
