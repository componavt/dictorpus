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
    
        @foreach ($genre->plots as $plot)
    <div class="subdiv">
    <h4>{{$plot->name}} ({{$plot->texts->count(0)}})</h4>
            @foreach ($plot->texts()->whereIn('lang_id', $lang_id)->get() as $text)
            @include('corpus.collection._text')
            @endforeach
    </div>
        @endforeach
    @endforeach

@stop
