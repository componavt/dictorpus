@extends('layouts.master')

@section('content')
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('navigation.about_project') }}</div>

                <div class="panel-body">{!! trans('blob.welcome_text') !!}</div>
            </div>
@endsection
