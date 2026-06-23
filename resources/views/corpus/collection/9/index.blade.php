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

    @foreach ($corpuses as $corpus)
    <h3 class='with-first-big-letter'>{{ $corpus->name}} ({{ $collection->countTextsForCorpus($corpus->id) }})</h3>

        @foreach ($collection->getPlots($corpus->id) as $plot)
            @if ($collection->countTextsForCorpus($corpus->id, $plot->id))
    <p><a href="{{ LaravelLocalization::localizeURL('/corpus/collection/'.$collection->id.'/texts?plot_id='.$plot->id.'&corpus_id='.$corpus->id.'&for_print='.$for_print) }}">{{$plot->name}}</a>
         ({{ $collection->countTextsForCorpus($corpus->id, $plot->id) }})</li>
            @endif
        @endforeach
    @endforeach
@stop
