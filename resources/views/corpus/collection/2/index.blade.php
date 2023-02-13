<?php $collection_id = 2; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.collections') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <h2>{{trans('collection.name_list')[$id]}}</h2>
    <p>{!!trans('collection.about')[$id]!!}</p>
    <p><b>{{trans('collection.total_count')}}:</b> {{$text_count}}</p>

    @foreach ($genres as $genre)
    <h3>{{$genre->name_pl}} ({{$genre->collectionTexts($collection_id)->count()}})</h3>
    <ul>
        @foreach ($genre->plots as $plot)
            @if ($plot->texts->count())
        <li><a href="{{ LaravelLocalization::localizeURL('/corpus/collection/2/'.$plot->id)}}">{{$plot->name}}</a> ({{$plot->texts->count()}})</li>
{{--                @foreach ($plot->texts()->whereIn('lang_id', $lang_id)->orderBy('sequence_number')->get() as $text)
    @include('corpus.collection._text', 
            ['event' => $text->event, 'source' => $text->source])
                @endforeach --}}
            @endif
        @endforeach
    </ul>
    @endforeach

@stop
