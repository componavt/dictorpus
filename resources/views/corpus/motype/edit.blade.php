@extends('layouts.page')

@section('page_title')
{{ trans('navigation.motypes') }}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_motype')}} <span class='imp'>"{{ $motype->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/motype/'.$motype->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($motype, array('method'=>'PUT', 'route' => array('motype.update', $motype->id))) !!}
        @include('corpus.motype._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop