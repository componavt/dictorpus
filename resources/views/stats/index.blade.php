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
    
    <h3>{{trans('stats.stats_by_users')}}</h3>
    <table class="table-bordered stats-table">
        <tr>
            <td>{{trans('stats.total_users')}}</td><td>{{$total_users}}</td>
        </tr>
        <tr>
            <td>{{trans('stats.total_active_editors')}}</td><td>{{$total_active_editors}}</td>
        </tr>
    </table>
@endsection
