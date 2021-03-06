@extends('layouts.page')

@section('page_title')
{{ trans('navigation.corpuses') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/corpus/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('corpus.store'))) !!}
        @include('corpus.corpus._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop