@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}. {{ trans('navigation.help') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    @include('help.text._search')
@stop
    