@extends('layouts.page')

@section('page_title')
{{ trans('navigation.authors') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/author/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('author.store'))) !!}
        @include('corpus.author._form_create_edit', ['submit_title' => trans('messages.create'),
                                      'author' => null,
                                      'action' => 'create'])
        @include('widgets.form.formitem._submit', ['title' =>  trans('messages.create_new_m')])
        {!! Form::close() !!}
@stop