@extends('layouts.page')

@section('page_title')
{{ trans('navigation.districts') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/district/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('district.store'))) !!}
        @include('corpus.district._form_create_edit', ['action' => 'create'])
        @include('widgets.form.formitem._submit', ['title' => trans('messages.create_new_m'])
        {!! Form::close() !!}
@stop