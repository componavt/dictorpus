@extends('layouts.page')

@section('page_title')
{{ trans('navigation.plots') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/plot/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('plot.store'))) !!}
        @include('corpus.plot._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop