@extends('layouts.page')

@section('page_title')
{{ trans('navigation.relations') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_relation')}} <span class='imp'>"{{ $relation->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/relation/'.$relation->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($relation, array('method'=>'PUT', 'route' => array('relation.update', $relation->id))) !!}
        @include('dict.relation._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop