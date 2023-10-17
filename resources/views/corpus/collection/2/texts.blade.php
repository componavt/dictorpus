<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('collection.name_list')[2] }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <p>
        <a href="{{ LaravelLocalization::localizeURL('/corpus/collection/2') }}">{{trans('collection.to_collection')}}</a>
        @if(isset($back_link)) 
        | <a href="{{ LaravelLocalization::localizeURL($back_link[0]) }}">{{$back_link[1]}}</a>
        @endif
    </p>
    
    <h2>{{$page_title}}</h2>
    
    @if (sizeof($texts)) 
    <table class="table table-striped table-wide wide-md">
    <thead>
        <tr>
            <th>No</th>
            <th>{{ trans('corpus.title') }}</th>
            <th>{{ trans('messages.translation') }}</th>
            <th>{{ trans('messages.year') }}</th>
            <th>{{ trans('corpus.record_place') }}</th>
            <th>{{ trans('corpus.informant') }}</th>
            <th>{{ trans('corpus.recorder') }}</th>
        </tr>
    </thead>
    
        @foreach ($texts as $text)
        <tr>
            <td data-th="No">{{ $list_count++ }}</td>
            <td data-th="{{ trans('corpus.title') }}">
                {{-- $text->authorsToString() ? $text->authorsToString().'.' : '' --}}
                <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.$url_args) }}">{{$text->title}}</a>
            </td>
            <td data-th="{{ trans('messages.translation') }}">
                @if ($text->transtext)
                {{-- $text->transtext->authorsToString() ? $text->transtext->authorsToString().'.' : '' --}}
                {{$text->transtext->title}}
                @endif
            </td>
            <td data-th="{{ trans('messages.year') }}">
                @if ($text->event && $text->event->date)
                    {{$text->event->date}}
                @endif
            </td>
            <td data-th="{{ trans('corpus.record_place') }}">
                @if ($text->event && $text->event->place)
                    @include('corpus.place._to_string',['place' => $text->event->place])
                @endif
            </td>
            <td data-th="{{ trans('corpus.informant') }}">
                @if ($text->event && sizeof($text->event->informants))
                    {{join(', ', $text->event->informantsWithLink())}}
                @endif
            </td>
            <td data-th="{{ trans('corpus.recorder') }}">
                @if ($text->event && sizeof($text->event->recorders))
                    {{join(', ', $text->event->recordersWithLink())}}
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    @endif
@stop
