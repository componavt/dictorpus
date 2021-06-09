@extends('layouts.page')

@section('page_title')
{{ trans('stats.by_user') }}
@endsection

@section('headExtra')
    {!!Html::style('css/stats.css')!!}
@stop

@section('body')
    <table class="table-bordered stats-table">
        <tr>
            <td>{{trans('stats.total_users')}}</td><td>{{$total_users}}</td>
        </tr>
        <tr>
            <td>{{trans('stats.total_editors')}}</td><td>{{$total_editors}}</td>
        </tr>
        <tr>
            <td>{{trans('stats.total_active_editors')}}</td><td>{{$total_active_editors}}</td>
        </tr>
    </table>
@stop
