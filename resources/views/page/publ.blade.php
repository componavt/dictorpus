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
    
    
    <a href="{{ LaravelLocalization::localizeURL('/page/mass_media') }}">{{ trans('navigation.mass_media')}}</h2>
</div>
@endsection
