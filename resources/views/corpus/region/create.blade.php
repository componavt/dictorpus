@extends('layouts.page')

@section('page_title')
{{ trans('navigation.regions') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/region/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('region.store'))) !!}
        @include('corpus.region._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create'])
        {!! Form::close() !!}
@stop