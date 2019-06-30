@extends('layouts.page')

@section('page_title')
{{ trans('navigation.publications') }}
@endsection

@section('body')
    {!! trans('blob.our_publications') !!}<br>
    
    @if(LaravelLocalization::getCurrentLocale() == 'ru')
    <h2>{{ trans('navigation.publications_about')}}</h2>
{!! trans('blob.publications_about') !!}
    @endif

</div>
@endsection
