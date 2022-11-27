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
    <div id="letter-b">
        <div id="letter-links">
        @foreach ($alphabet as $letter)
        <a class="{{$url_args['search_letter'] == $letter->letter ? 'letter-active' : '' }}" onClick="viewLetter('{{$locale}}', this)">{{$letter->letter}}</a>
        @endforeach
        </div>
        <div id="gram-links">
            @include('olodict._gram_links')    
        </div>
    </div>

    <div id="lemmas-b">
    @if ($url_args['search_lemma'])
        @include('olodict._lemmas')    
    @else
        <div class="page-b">{!! trans('olodict.welcome_block') !!}</div>
        <!--p><a href="{{ LaravelLocalization::localizeURL('/olodict/help')}}">{{trans('olodict.help_title')}}</a></p-->

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
    selectConcept('search_concept_category', 'search_pos', '{{trans('dict.concept')}}', true, '{{$label_id}}');
    $(".select-pos").select2({
        allowClear: true,
        placeholder: '{{trans('dict.pos')}}',
        width: '100%'
    });
    $(".select-pos").select2({
        allowClear: true,
        placeholder: '{{trans('dict.pos')}}',
        width: '100%'
    });
    $(".select-topic").select2({
        allowClear: true,
        placeholder: '{{trans('olodict.topic')}}',
        width: '100%'
    });
@stop