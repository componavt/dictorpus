<?php $count = 1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<h2>Поиск закономерностей в чередовании гласных у ливвиковских глаголов</h2>
<p>у которых с.ф. на ua, а 3 л. ед. ч. през. имперф. на oi</p>

<table class="table-bordered">
    <tr>
        <th>N</th>
        <th>лемма</th>
        <th>имперфект 3 л. ед.ч.</th>
        
        @foreach ($lemmas as $lemma)
        <tr style="text-align: right">
            <td>{{$count++}}</td>
            <td><a href="/dict/lemma/{{$lemma->id}}">{{$lemma->lemma}}</a></td>
            <td>{{$lemma->wordform}}</td>
        </tr>
        @endforeach
    </tr>
</table>
@endsection