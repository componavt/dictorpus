@extends('layouts.master')

@section('title')
{{ trans('navigation.permission') }}
@endsection

@section('content')
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('navigation.permission') }}</div>

                <div class="panel-body">{!! trans('blob.permission')!!}</div>
            </div>
@endsection
