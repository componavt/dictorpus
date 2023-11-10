<?php $locale = LaravelLocalization::getCurrentLocale(); ?>    
@extends('layouts.ldl')

@section('headExtra')
    {!!Html::style('css/essential_audio.css')!!}
    {!!Html::style('css/essential_audio_circle.css')!!}
@stop

@section('clear_b')
    @include('ldl._alphabet')
@stop    

@section('body')
<div class="row">
    <div class="col-sm-8">
        <h1>{{ $concept->text }}</h1>
        <p>{{ $concept->descr}}</p>
        <p><b>{{ trans('dict.pos') }}:</b> {{ $concept->pos->name}}</p>
    </div>
    <div class="col-sm-4 concept-page-photo">
        <div id='concept-photo_{{$concept->id}}'></div> 
    </div>
</div>

    @foreach ($lemmas as $lemma)
    <div class="row lemma-b">
        <div class="col-sm-4">
            <h2>
                {{ $lemma->lemma }}
                @if ($lemma->audios->first())
                    @include('widgets.audio_decor', ['route'=>$lemma->audios->first()->url()])
                @endif
            </h2>
        @foreach ($lemma->meanings as $meaning)
            <div class='meaning-b'>
            @if (sizeof($lemma->meanings) >1)
                {{$meaning->meaning_n}}.
            @endif
                {{ $meaning->textByLangCode($locale, 'ru') }}

            @if ($meaning->places()->count())
                <p>
                    <b>{{ trans('dict.places_use') }}</b>: {{ join(', ', $meaning->places->pluck('name')->toArray()) }}
                </p>
            @endif
            </div>
        @endforeach
        </div>
        <div>
            <div class="lemma-meaning-examples">
                <img class="img-loading" id="img-loading_{{$meaning->id}}" src="{{ asset('images/loading.gif') }}">
                <div  id="meaning-examples_{{$meaning->id}}"></div>
            </div>
            
        </div>
    </div>
    @endforeach

@stop

@section('footScriptExtra')
    {!!Html::script('js/essential_audio.js')!!}
    {!!Html::script('js/meaning.js')!!}
@stop

@section('jqueryFunc')
    loadPhoto('concept', {{$concept->id}}, '/dict/concept/{{$concept->id}}/photo_preview');
    @foreach ($lemmas as $lemma)
        @foreach ($lemma->meanings as $meaning)
            loadExamples('{{LaravelLocalization::localizeURL('/ldl/meaning/examples/load')}}', {{$meaning->id}}, 0, 0);
        @endforeach
    @endforeach
@stop

