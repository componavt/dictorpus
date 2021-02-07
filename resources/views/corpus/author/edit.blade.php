@extends('layouts.page')

@section('page_title')
{{ trans('navigation.authors') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_author')}} <span class='imp'>"{{ $author->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/author/'.$author->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($author, array('method'=>'PUT', 'route' => array('author.update', $author->id))) !!}
        @include('corpus.author._form_create_edit', ['action' => 'edit'])
        @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
        {!! Form::close() !!}
@stop