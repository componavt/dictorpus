@extends('layouts.page')

@section('page_title')
{{ trans('navigation.labels') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_label')}} <span class='imp'>"{{ $label->name }}"</span></h2>
        <p><a href="{{ route('label.show', $label->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($label, array('method'=>'PUT', 'route' => array('label.update', $label->id))) !!}
        @include('dict.label._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop