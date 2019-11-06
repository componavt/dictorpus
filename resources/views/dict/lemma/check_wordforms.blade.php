<?php $count = 1 ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('body')
    @foreach ($lemmas as $pos_name=>$lemmas_pos)
    <h3>{{$pos_name}}</h3>
        @foreach ($lemmas_pos as $lemma_id=>$lemma)
    <p>{{$count++}}. <a href="/dict/lemma/{{$lemma_id}}">{{$lemma['lemma']}}</a></p>
    <table class="table-bordered">
            @foreach ($lemma['dialects'] as $dialect_name => $dialect_gramset)
        <tr><th colspan="3">{{$dialect_name}}</th></tr>
                @foreach ($dialect_gramset as $gramset_name => $wordforms)
        <tr>
            <td>{{$gramset_name}}</td>
            <td>{{$wordforms[0]}}</td>
            <td>{{$wordforms[1]}}</td>
        </tr>
                @endforeach
            @endforeach
    </table>
        @endforeach
    @endforeach
@stop

