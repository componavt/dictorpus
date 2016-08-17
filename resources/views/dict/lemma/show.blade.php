@extends('layouts.app')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('content')
    <div class="container">

        <h2>{{ $lemma->lemma }}</h2>
        
        @foreach ($lemma->meanings as $meaning)
        <div>
            {{$meaning->meaning_n}}.
            <ul>
            @foreach ($meaning->meaningTexts as $meaning_text)
                <li>{{$meaning_text->lang->name}}: {{$meaning_text->meaning_text}}</li>
            @endforeach
            </ul>
        </div>
        @endforeach
        
    </div>
@stop


