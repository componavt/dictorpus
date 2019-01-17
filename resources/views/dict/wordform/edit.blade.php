@extends('layouts.master')

@section('title')
{{ trans('navigation.wordforms') }}
@stop

@section('content')
        <h1>{{ trans('navigation.wordforms') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_wordform')}}: <span class='imp'>{{ $wordform->wordform}}</span></h2>
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/wordform/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
        </p>
        
        {!! Form::model($wordform, array('method'=>'PUT', 'route' => array('wordform.update', $wordform->id))) !!}
        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
        
        @include('widgets.form.formitem._text', 
                ['name' => 'wordform', 
                 'special_symbol' => true,
                 'title'=>trans('dict.wordform')])

        @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
                 
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop

