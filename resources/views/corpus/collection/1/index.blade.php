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
    <h3>{{$genre->name_pl}}</h3>
        @foreach ($dialects as $dialect)
            @if (sizeof($dialect->textsByGenre($genre->id)))
    <div class="subdiv">
    <h4>{{$dialect->name}} {{trans('dict.dialect')}} ({{sizeof($dialect->textsByGenre($genre->id))}})</h4>
                @foreach ($dialect->textsByGenre($genre->id)->sortBy('title') as $text)
    @include('corpus.collection._text', 
            ['event' => $text->event, 'source' => $text->source, 'lang_id'=>$lang_ids[0]])
                @endforeach
    </div>
            @endif
        @endforeach
    @endforeach
@stop
