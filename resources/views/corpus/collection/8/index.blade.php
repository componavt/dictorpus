@extends('layouts.page')

@section('page_title')
{{ trans('navigation.collections') }}
@stop

@section('headExtra')
    {!! css('text')!!}
@stop

@section('body')
    <div class='row'>
        <div class='col-sm-8'>
            <h2>{{trans('collection.name_list')[$id]}}</h2>
            <p>{!!trans('collection.about')[$id]!!}</p>
        </div>
        <div class='col-sm-4 author_b'>
            <img src="/images/nina_zaitseva.jpg">
        </div>
    </div>

    @foreach ($author->textsByCorpuses() as $corpus_name => $corpus_texts)
    <h2 class='fletter-capitalize'>{{ $corpus_name }}</h2>
        @foreach ($corpus_texts as $genre_name => $genre_texts)
    <h3>{{ $genre_name }}</h3>
            @foreach ($genre_texts as $text)
                @include('corpus.collection._text', 
                    ['event' => $text->event, 'source' => $text->source])
            @endforeach
        @endforeach
    @endforeach
@stop
