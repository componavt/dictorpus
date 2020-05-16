<?php $count = 1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<h2>Поиск закономерностей в чередовании гласных</h2>
<p>Словоформы имен (существительные и прилагательные) ливвиковского младописьменного варианта, которые
    в форме номинатива ед. заканчиваются на {{$num==1 ? 'u или a' : 'y или ä'}}, при этом в форме генитива ед. заканчиваются на {{$num==1 ? 'a' : 'ä'}}n</p>

<table class="table-bordered">
    <tr>
        <th>N</th>
        <th>номинатив ед.ч.</th>
        <th>генитив ед.ч.</th>
        <th>партитив мн.ч.</th>
        
        @foreach ($words as $pos_name => $pos_words) 
        <tr><td colspan="4" style="text-align:center; font-weight: bold">{{$pos_name}}</td></tr>
            @foreach ($pos_words as $lemma_id => $lemma)
            <tr>
                <td>{{$count++}}</td>
                <td><a href="/dict/lemma/{{$lemma_id}}">{{$lemma['nom']}}</a></td>
                <td>{{$lemma['gen']}}</td>
                <td>{{$lemma['part']}}</td>
            </tr>
            @endforeach
        @endforeach
    </tr>
</table>
@endsection