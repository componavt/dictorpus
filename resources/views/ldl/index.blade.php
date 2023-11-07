@extends('layouts.ldl')

@section('clear_b')
    @include('ldl._alphabet')

    <div class="row">
    @foreach ($concepts as $concept)
        <div class="col-sm-3">
            <div class="concept-b">
                <p class="concept-t">
                    <a href="{{ LaravelLocalization::localizeURL('/ldl/concept/'.$concept->id)}}">{{ $concept->text }}</a>
                </p>
                <a href="{{ LaravelLocalization::localizeURL('/ldl/concept/'.$concept->id)}}">
                    <div id='concept-photo_{{$concept->id}}' class='concept-photo'></div> 
                </a>
                <img class="img-loading" id="img-photo-loading_{{$concept->id}}" src="{{ asset('images/loading_small.gif') }}">
             </div>
        </div>
    @endforeach
    </div>

@stop

@section('footScriptExtra')
    {!!Html::script('js/meaning.js')!!}
@stop

@section('jqueryFunc')
    @foreach($concepts as $concept)
        loadPhoto('concept', {{$concept->id}}, '/dict/concept/{{$concept->id}}/photo_preview', 0);
    @endforeach
@stop

