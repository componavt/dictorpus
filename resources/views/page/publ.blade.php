@extends('layouts.page')

@section('page_title')
{{ trans('navigation.publications') }}
@endsection

@section('body')
    {!! trans('page.our_publications') !!}<br>

    <div class="row" style='margin-bottom: 20px'>
        @include('page.video_1_from_3', ['video' => 'dN_o4rgpTbQ'])
        @include('page.video_1_from_3', ['video' => 'cUpqM97LXGs'])
        @include('page.video_1_from_3', ['video' => '0coYBYlJmKY'])
    </div>
    
    
    <h2>{{ trans('navigation.publications_about')}}</h2>
    {!! trans('page.publications_about') !!}

    <div class="row">
        @include('page.video_1_from_3', ['video' => 'm-QQW85U8U4'])
        @include('page.video_1_from_3', ['video' => 'tYH611xhZE0'])
        @include('page.video_1_from_3', ['video' => 'd6DnVDVFwGQ'])
        @include('page.video_1_from_3', ['video' => 'rDTEKEQd7YI'])
        @include('page.video_1_from_3', ['video' => '3DLsfO-c1Hc'])
    </div>
</div>
@endsection
