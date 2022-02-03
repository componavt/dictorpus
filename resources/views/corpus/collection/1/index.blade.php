@extends('layouts.page')

@section('page_title')
{{ trans('navigation.collections') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <h2>{{trans('collection.name_list')[$id]}}</h2>
    @foreach ($genres as $genre)
    <h3>{{$genre->name_pl}}</h3>
        @foreach ($dialects as $dialect)
            @if (sizeof($dialect->textsByGenre($genre->id)))
    <div class="subdiv">
    <h4>{{$dialect->name}} {{trans('dict.dialect')}} ({{sizeof($dialect->textsByGenre($genre->id))}})</h4>
                @foreach ($dialect->textsByGenre($genre->id) as $text)
    @include('corpus.collection._text', 
            ['event' => $text->event, 'source' => $text->source])
                @endforeach
    </div>
            @endif
        @endforeach
    @endforeach
@stop
