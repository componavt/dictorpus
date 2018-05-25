@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('content')
        <h2>{{trans('dict.last_updated_lemmas')}}</h2>
        @foreach ($last_updated_lemmas as $cr_date =>$lemmas)
        <p class="date">{{$cr_date}}</p>
            @foreach ($lemmas as $lemma)
            <div class="date-b">
                <div class="time">{{$lemma->updated_at->formatLocalized("%H:%M")}}</div>
                <div class="event">
                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma')}}/{{$lemma->id}}">{{$lemma->lemma}}</a> 
                    ({{$lemma->user}})
                </div>
            </div> 
            @endforeach
        @endforeach
@stop


