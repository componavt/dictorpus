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
        @include('corpus.collection.2._genre', ['genre'=>$genre, 'header'=>3])
        @if ($genre->children()->count())
        <div style='padding-left: 20px;'>
            @foreach ($genre->children as $subgenre)
                @include('corpus.collection.2._genre', ['genre'=>$subgenre, 'header'=>4])
            @endforeach
        </div>
        @endif
    @endforeach

    <h4 style='margin-top: 20px;'><a href="{{ LaravelLocalization::localizeURL('/corpus/collection/2/topics') }}">{{trans('collection.topic_index')}}</a></h4>
@stop
