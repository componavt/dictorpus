@extends('layouts.'.($for_print ? 'for_print' : 'page'))

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

    @foreach ($collection->getPlots() as $plot)
    <h3>{{ $plot->name }} ({{ $collection->countTextsForPlot($plot->id) }})</h3>
    <ul>
        @foreach ($collection->getTopicsForPlot($plot->id) as $topic)
        <li><a href="{{ LaravelLocalization::localizeURL('/corpus/collection/'.$collection->id.'/texts?plot_id='.$plot->id.'&topic_id='.$topic->id.'&for_print='.$for_print) }}">
            {{ $topic->name }}</a>
            ({{ $collection->countTextsForTopic($topic->id, $plot->id) }})
        </li>
        @endforeach
    </ul>
    @endforeach
@stop
