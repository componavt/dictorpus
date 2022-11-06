<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('collection.name_list')[3] }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <h2>{{trans('corpus.cycle')}}: {{$cycle->name}}</h2>
    
    @if (sizeof($texts)) 
    <table class="table table-striped table-wide wide-md">
    <thead>
        <tr>
            <th>No</th>
            <th>{{ trans('dict.dialect') }}</th>
            <th>{{ trans('corpus.motive') }}</th>
            <th>{{ trans('corpus.title') }}</th>
            <th>{{ trans('messages.translation') }}</th>
        </tr>
    </thead>
    
        @foreach ($texts as $text)
        <tr>
            <td data-th="No">{{ $list_count++ }}</td>
            <td data-th="{{ trans('dict.dialect') }}">
                @if($text->dialects)
                    @foreach ($text->dialects as $dialect)
                    {{$dialect->name}}<br>
                    @endforeach

                @endif
            </td>
            <td data-th="{{ trans('corpus.motive') }}">
                {!! $text->motivesToString('/collection/3/'.$cycle->id.'/', '<br>', true) !!}
            </td>
            <td data-th="{{ trans('corpus.title') }}">
                {{-- $text->authorsToString() ? $text->authorsToString().'.' : '' --}}
                <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'?search_collection=3&search_cycle='.$cycle->id) }}">{{$text->title}}</a>
            </td>
            <td data-th="{{ trans('messages.translation') }}">
                @if ($text->transtext)
                {{-- $text->transtext->authorsToString() ? $text->transtext->authorsToString().'.' : '' --}}
                {{$text->transtext->title}}
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    @endif
@stop
