@extends('layouts.page')

@section('page_title')
{{ trans('navigation.collections') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <div class='row'>
        <div class='col-sm-8'>
            <h2>{{trans('collection.name_list')[$id]}}</h2>
            <p>{!!trans('collection.about')[$id]!!}</p>
        </div>
        <div class='col-sm-4 author_b'>
            <img src="/images/stanislav_tarasov.jpg">
            <p style="margin-top: 10px"><i>Фото с сайта <a href="https://tverinkarjala.pictures.fi/kuvat/">"Matkakuvia Tverin Karjalaiskylistä 1997-2018"</i></a></p>
        </div>
    </div>
    
    @foreach ($author->texts as $text)
        @include('corpus.collection._text', 
                ['event' => $text->event, 'source' => $text->source])
    @endforeach
@stop
