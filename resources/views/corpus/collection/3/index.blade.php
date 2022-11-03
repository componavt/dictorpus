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
    <h3>{{trans('collection.prediction_cycles')}}</h3>
    <ol>
    @foreach ($genres[0]->cycles as $cycle)
{{--            @if ($cycle->texts->count()) --}}
        <li><a href="{{ LaravelLocalization::localizeURL('/corpus/collection/3/'.$cycle->id) }}">{{$cycle->name}}</a> ({{$cycle->texts->count()}})</li>
{{--                @foreach ($cycle->texts()->whereIn('lang_id', $lang_id)->get() as $text)
    @include('corpus.collection._text', 
            ['event' => $text->event, 'source' => null])
                @endforeach
            @endif --}}
        @endforeach
    </ol>
@stop
