@extends('layouts.page')

@section('page_title')
{{ trans('navigation.references_geo') }}
@endsection

@section('body')
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/place') }}">{{ trans('navigation.places') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/district') }}">{{ trans('navigation.districts') }}</a></p>
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/region') }}">{{ trans('navigation.regions') }}</a></p>
@endsection
