@extends('layouts.page')

@section('page_title')
{{ trans('navigation.references_gram') }}
@endsection

@section('body')
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/pos') }}">{{ trans('navigation.parts_of_speech') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/gramset') }}">{{ trans('navigation.gramsets') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/gram') }}">{{ trans('navigation.grams') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/dict/relation') }}">{{ trans('navigation.relations') }}</a></p>
@endsection
