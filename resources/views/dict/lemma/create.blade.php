@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma_form.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.lemmas') }}</h1>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('lemma.store'))) !!}
        @include('dict.lemma._form_create_edit', ['submit_title' => trans('messages.create_new_f'),
                                      'action' => 'create',
                                      'lang_values' => $lang_values, 
                                      'pos_values'  => $pos_values])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/meaning.js')!!}
@stop

@section('jqueryFunc')
    addMeaning();
@stop
