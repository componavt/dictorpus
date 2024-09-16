@extends('layouts.'.($for_print ? 'for_print' : 'page'))

@section('page_title')
{{ trans('collection.name_list')[2] }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <p>
        <a href="{{ LaravelLocalization::localizeURL('/corpus/collection/2?for_print='.$for_print) }}">{{trans('collection.to_collection')}}</a>
    </p>
    <h2>{{trans('collection.topic_index')}}</h2>

    @foreach ($genres as $genre)
        @include('corpus.collection.2._genre_plots', ['genre'=>$genre, 'header'=>3])
        @if ($genre->children()->count())
        <div style='padding-left: 20px;'>
            @foreach ($genre->children as $subgenre)
                @include('corpus.collection.2._genre_plots', ['genre'=>$subgenre, 'header'=>4])
            @endforeach
        </div>
        @endif
    @endforeach

@stop
