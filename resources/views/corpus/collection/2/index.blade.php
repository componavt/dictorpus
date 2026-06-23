@extends('layouts.'.($for_print ? 'for_print' : 'page'))

@section('page_title')
{{ trans('navigation.collections') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <h2>{{trans('collection.name_list')[$collection->id]}}</h2>
    <p>{!!trans('collection.about')[$collection->id]!!}</p>
    <p><b>{{trans('collection.total_count')}}:</b> {{$text_count}}</p>

    @foreach ($genres as $genre)
        @include('corpus.collection.'.$collection->id.'._genre', ['genre'=>$genre, 'header'=>3])
        @if ($genre->children()->count())
        <div style='padding-left: 20px;'>
            @foreach ($genre->children as $subgenre)
                @include('corpus.collection.'.$collection->id.'._genre', ['genre'=>$subgenre, 'header'=>4])
            @endforeach
        </div>
        @endif
    @endforeach

    <h4 style='margin-top: 20px;'><a href="{{ LaravelLocalization::localizeURL('/corpus/collection/'.$collection->id.'/topics?for_print='.$for_print) }}">{{trans('collection.topic_index')}}</a></h4>
@stop
