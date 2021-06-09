@extends('layouts.page')

@section('page_title')
{{ trans('navigation.stats') }}
@endsection

@section('headExtra')
    {!!Html::style('css/stats.css')!!}
@stop

@section('body')
    <p><a href="{{LaravelLocalization::localizeURL('/stats/by_dict')}}">{{trans('stats.stats_by_dict')}}</a></p>
    <p><a href="{{LaravelLocalization::localizeURL('/stats/by_corp')}}">{{trans('stats.stats_by_corp')}}</a></p>
    <p><a href="{{LaravelLocalization::localizeURL('/stats/by_user')}}">{{trans('stats.by_user')}}</a></p>
@stop
