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
    <h3>{{$genre->name_pl}} ({{ $dialects[$genre->id]['genre_text_count'] }})</h3>
        @foreach ($langs as $lang) 
            @if (!empty($dialects[$genre->id]['langs'][$lang->id]['lang_text_count']))
    <h4>{{ $lang->short }} ({{ $dialects[$genre->id]['langs'][$lang->id]['lang_text_count'] }})</h4>
                @foreach ($dialects[$genre->id]['langs'][$lang->id]['dialects'] as $dialect_id =>$dialect)
    <div class="subdiv">
    <h5>{{$dialect['dialect']->name}} {{ trans('dict.dialect') }} ({{ sizeof($dialect['texts']) }})</h5>
                    @foreach ($dialect['texts'] as $text)
    @include('corpus.collection._text', 
            ['event' => $text->event, 'source' => $text->source, 'lang_id'=>$lang->id])
                    @endforeach
    </div>
                @endforeach
            @endif
        @endforeach
    @endforeach
@stop
