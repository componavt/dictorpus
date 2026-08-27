<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.source_publications') }}
@stop

@section('headExtra')
    {!! css('text') !!}
@stop

@section('body')
    <div class="row">
        <div class="col-sm-8">
            <h2>{{ $publication->title }}</h2>

            <p><a href="{{ route('publication.index', $url_args) }}">{{ trans('messages.back_to_list') }}</a></p>

            <p><b>{{ trans('corpus.authors') }}</b>: {{ $publication->authors }}</p>
            <p><b>{{ trans('corpus.title') }}</b>: {{ $publication->title }}</p>
            <p><b>{{ mb_ucfirst(trans('messages.year')) }}</b>: {{ $publication->year }}</p>

            @if ($publication->pubparts()->count())
            <p><b>{{ trans('corpus.pubparts') }}:</b></p>
            <div class="topic-list">
            @foreach ($publication->pubparts as $pubpart)
                {{ $pubpart->full_name }} (
                @if ($pubpart->texts()->count())<a href="{{ route('text.index', ['search_pubpart'=>$pubpart]) }}">@endif{{ $pubpart->texts()->count() }}@if ($pubpart->texts()->count())</a>@endif
                )<br>
            @endforeach
            </div>
            @endif
        </div>
        <div class="col-sm-4" style='text-align: right'>
            @include('corpus.publication.photo')
        </div>
    </div>
@stop


