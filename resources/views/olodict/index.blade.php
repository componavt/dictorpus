@extends('layouts.olodict')

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/essential_audio.css')!!}
    {!!Html::style('css/essential_audio_circle.css')!!}
@stop

@section('left-column')
    <div id="lemma-list">
        @include('olodict._lemma_list')
    </div>
    @include('olodict._search_form')
@stop

@section('body')
    <div id="letter-links">
        @foreach ($alphabet as $letter)
        <a class="{{$url_args['search_letter'] == $letter->letter ? 'letter-active' : '' }}" onClick="viewLetter('{{$locale}}', this)">{{$letter->letter}}</a>
        @endforeach
    </div>

    <div id="gram-links">
        @include('olodict._gram_links')    
    </div>

    <div id="lemmas-b">
    @if ($url_args['search_lemma'])
        @include('olodict._lemmas')    
    @else
        <p>{!! trans('olodict.welcome_block') !!}</p>
        <p><a href="{{ LaravelLocalization::localizeURL('/olodict/help')}}">{{trans('olodict.help_title')}}</a></p>
        <p><a href="{{ LaravelLocalization::localizeURL('/olodict/abbr')}}">{{trans('olodict.abbr_title')}}</a></p>

    @endif
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/essential_audio.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/olodict.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    selectConcept('search_concept_category', 'search_pos', '{{trans('dict.concept')}}', true);
@stop