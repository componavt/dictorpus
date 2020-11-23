@extends('layouts.page')

@section('page_title')
{{ trans('navigation.parts_of_speech') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_pos')}} <span class='imp'>"{{ $pos->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/pos/'.$pos->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($pos, array('method'=>'PUT', 'route' => array('pos.update', $pos->id))) !!}
        @include('dict.pos._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop