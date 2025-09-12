@extends('layouts.page')

@section('page_title')
{{ trans('navigation.synsets') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')      
    @include('dict.synset._'.$action)
@stop

@section('footScriptExtra')
    {!! js('synset') !!}
@stop

@section('jqueryFunc')
@stop
