@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_lemma')}}: {{ $lemma->lemma}}</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a></p>
        
        <p><b>{{ trans('dict.lang') }}:</b> {{ $lemma->lang->name}}</p>
        <p><b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}</p>

        <h3>{{ trans('dict.wordforms') }}</h3>
        {!! Form::model($lemma, array('method'=>'PUT', 'route' => array('lemma_wordform.update', $lemma->id))) !!}
        @include('dict.lemma_wordform._form_edit')
        @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop