@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('content')
        <h2>{{ $lemma->lemma }}</h2>
        
        <p><b>{{ trans('messages.pos') }}:</b> {{ $lemma->pos->name}}</p>
        
        @foreach ($lemma->meanings as $meaning)
        <div>
            {{$meaning->meaning_n}}.
            <ul>
            @foreach ($meaning->meaningTexts as $meaning_text)
                <li><b>{{$meaning_text->lang->name}}:</b> {{$meaning_text->meaning_text}}</li>
            @endforeach
            </ul>
        </div>
        @endforeach
        
        @if ($lemma->wordforms()->count())
        <p><b>{{ trans('messages.wordforms') }}</b></p>
        <table class="table-bordered">
            @foreach ($lemma->wordforms as $key=>$wordform)
            <tr>
                <td>{{$key+1}}.</td>
                <td>{{ $wordform->wordform}}</td>
                @if($lemma->hasGramsets())
                <td>
                    @if ($wordform->lemmaDialectGramset($lemma->id))
                    {{ $wordform->lemmaDialectGramset($lemma->id)->gramsetList()}}
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
        </table>
        @endif
@stop


