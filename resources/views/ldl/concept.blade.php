@extends('layouts.ldl')

@section('clear_b')
    @include('ldl._alphabet')
@stop    

@section('body')
<div class="row">
    <div class="col-sm-8">
        <h1>{{ $concept->text }}</h1>
        <p><b>{{ trans('dict.pos') }}:</b> {{ $concept->pos->name}}</p>
    </div>
    <div class="col-sm-4 concept-page-photo">
        <div id='concept-photo_{{$concept->id}}'></div> 
    </div>
</div>

    @foreach ($lemmas as $lemma)
    <div>
        <h2>{{ $lemma->lemma }}</h2>
    </div>
    @endforeach

@stop

@section('footScriptExtra')
    {!!Html::script('js/meaning.js')!!}
@stop

@section('jqueryFunc')
    loadPhoto('concept', {{$concept->id}}, '/dict/concept/{{$concept->id}}/photo_preview');
@stop

