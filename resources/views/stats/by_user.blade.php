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

    @if (is_editor())
    <p style='margin-top: 20px'><a href="{{ route('stats.by_editors') }}">{{ trans('stats.stats_by_editors') }}</a></p>
    @endif
@stop
