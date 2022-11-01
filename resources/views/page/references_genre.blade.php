@extends('layouts.page')

@section('page_title')
{{ trans('navigation.references_genre') }}
@endsection

@section('body')
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/genre') }}">{{ trans('navigation.genres') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/cycle') }}">{{ trans('navigation.cycles') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/motype') }}">{{ trans('navigation.motypes') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/motive') }}">{{ trans('navigation.motives') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/plot') }}">{{ trans('navigation.plots') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/topic') }}">{{ trans('navigation.topics') }}</a></p>
@endsection
