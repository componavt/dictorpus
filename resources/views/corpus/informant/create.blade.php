@extends('layouts.page')

@section('page_title')
{{ trans('navigation.informants') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/informant/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('informant.store'))) !!}
        @include('corpus.informant._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create',
                                      'place_values' => $place_values])
        {!! Form::close() !!}
@stop