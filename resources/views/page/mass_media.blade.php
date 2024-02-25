@extends('layouts.page')

@section('page_title')
{{ trans('navigation.mass_media') }}
@endsection

@section('body')
    <h2>{!! trans('navigation.mass_media_write') !!}</h2>
    {!! trans('page.mass_media_write') !!}

    <h2>{!! trans('navigation.mass_media_talk') !!}</h2>
    {!! trans('page.mass_media_talk') !!}

    <h2>{!! trans('navigation.mass_media_show') !!}</h2>
    <div class="row">
        @include('page.video_1_from_3', ['video' => 'F2OK-J_YlB0'])
        @include('page.video_1_from_3', ['video' => 'z2iBaJVTBaA'])
        @include('page.video_1_from_3', ['video' => 'BDhjFRK3HIA'])
        @include('page.video_1_from_3', ['video' => 'J0gnMVTd9SI'])
        @include('page.video_1_from_3', ['video' => 'KdlBe_s7hd8'])
        @include('page.video_1_from_3', ['video' => 'm-QQW85U8U4'])
        @include('page.video_1_from_3', ['video' => 'tYH611xhZE0'])
        @include('page.video_1_from_3', ['video' => 'd6DnVDVFwGQ'])
        @include('page.video_1_from_3', ['video' => 'rDTEKEQd7YI'])
        @include('page.video_1_from_3', ['video' => '3DLsfO-c1Hc'])
    </div>
</div>
@endsection
