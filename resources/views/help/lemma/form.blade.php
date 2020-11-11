@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}. {{ trans('navigation.help') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')
    @include('help.lemma._form')
@stop
    