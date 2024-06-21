<?php $locale = LaravelLocalization::getCurrentLocale(); ?>    
@extends('layouts.ldl')

@section('headExtra')
    {!! css('essential_audio') !!}
    {!! css('essential_audio_circle') !!}
    {!! css('table') !!}
    {!! css('lemma') !!}
    {!! css('text') !!}
@stop

@section('clear_b')
    @include('ldl._alphabet')
@stop    

@section('body')
    <div class="row">
        <div class="col-sm-8">
            <h1>{{ $concept->text }}</h1>
            <p><big>{{ $concept->descr}}</big></p>
            <p><b>{{ trans('dict.pos') }}:</b> {{ $concept->pos->name}}</p>
        </div>
        <div class="col-sm-4 concept-page-photo">
            <div id='concept-photo_{{$concept->id}}'></div> 
        </div>
    </div>

    @foreach ($lemmas as $lemma)
        @include('ldl._lemma')
    @endforeach

@stop

@section('footScriptExtra')
    {!!Html::script('js/essential_audio.js')!!}
    {!!Html::script('js/meaning.js')!!}
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/text.js')!!}
@stop

@section('jqueryFunc')
    loadPhoto('concept', {{$concept->id}}, '/dict/concept/{{$concept->id}}/photo_preview');
    
    showAudioInfo();
    
    @foreach ($lemmas as $lemma)
        @foreach ($lemma->meanings as $meaning)
            loadExamples('{{LaravelLocalization::localizeURL('/ldl/meaning/examples/load')}}', {{$meaning->id}}, 0, 0);
        @endforeach
    @endforeach

{{-- show/hide a block with lemmas --}}
    showWordBlock('{{LaravelLocalization::getCurrentLocale()}}'); 
@stop

