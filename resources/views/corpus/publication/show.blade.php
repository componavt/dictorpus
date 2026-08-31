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

            <p>
                <a href="{{ route('publication.index', $url_args) }}">{{ trans('messages.back_to_list') }}</a>
        @if (User::checkAccess('ref.edit'))
            | @include('widgets.form.button._edit', ['route' => route('publication.edit', ['id'=>$publication->id] + $url_args)])
        @else
            | {{ trans('messages.edit') }}
        @endif 
            </p>

            <p><b>{{ trans('corpus.authors') }}</b>: {{ $publication->authors }}</p>
            <p><b>{{ trans('corpus.title') }}</b>: {{ $publication->title }}</p>
            <p><b>{{ mb_ucfirst(trans('messages.year')) }}</b>: {{ $publication->year }}</p>

            @include('corpus.pubpart.index')
        </div>
        <div class="col-sm-4" style='text-align: right'>
            @include('corpus.publication.photo')
        </div>
    </div>
@stop

@section('footScriptExtra')
    {!! js('publication')!!}
@stop

@section('jqueryFunc')
    initPublicationShowPubparts();
@stop
