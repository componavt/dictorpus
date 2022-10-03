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

        @foreach ($genres[0]->cycles as $cycle)
            @if ($cycle->texts->count())
    <div class="subdiv">
        <h4>{{$cycle->name}} ({{$cycle->texts->count()}})</h4>
                @foreach ($cycle->texts()->whereIn('lang_id', $lang_id)->get() as $text)
    @include('corpus.collection._text', 
            ['event' => $text->event, 'source' => null])
                @endforeach
    </div>
            @endif
        @endforeach

@stop
