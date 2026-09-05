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

    @foreach ($collection->getPlots($corpus_ids) as $plot)
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
    
    <a href="{{ route('collection.9.pointer') }}">Указатель основных сюжетов и мотивов/тем карельских мифологических рассказов (быличек). Часть 1. Мифологические существа, связанные с праздниками</a>
@stop
