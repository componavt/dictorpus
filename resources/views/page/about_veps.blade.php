@extends('layouts.page')

@section('page_title')
{{ trans('navigation.about_veps') }}
@endsection

@section('body')
    <div class="text-with-photo">
        <div>{!! trans('page.about_veps') !!}</div>
        <div class="photo-right-to-text">
            <img src="/images/KV.png">
            <div style="display:flex; margin-top: 20px;">
                <span style="width:50px; height:40px; background-color:#cf101a; margin-right: 20px;"></span>{!! trans('page.about_veps_stats') !!}
            </div>
        </div>
    </div>
@endsection
