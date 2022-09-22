@extends('layouts.olodict')

@section('headExtra')
    {!!Html::style('css/essential_audio.css')!!}
    {!!Html::style('css/essential_audio_circle.css')!!}
    {!!Html::style('css/mic.css')!!}
@stop

@section('left-column')
    <div id="lemma-list">
        @include('olodict._lemma_list')
    </div>
@stop

@section('body')
    <div id="letter-links">
        @foreach ($alphabet as $letter)
        <a class="{{$url_args['search_letter'] == $letter->letter ? 'letter-active' : '' }}" onClick="viewLetter(this, '{{$letter->letter}}')">{{$letter->letter}}</a>
        @endforeach
    </div>

    <div id="gram-links">
        @include('olodict._gram_links')    
    </div>

    <div id="lemmas-b">
        @include('olodict._lemmas')    
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/essential_audio.js')!!}
    {!!Html::script('js/olodict.js')!!}
@stop
