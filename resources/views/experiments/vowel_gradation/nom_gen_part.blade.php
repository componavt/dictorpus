<?php $count = 1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<h2>Поиск закономерностей в чередовании гласных</h2>
<p><b>{{$sl}}</b>-сложные <b>{{$pos_name}}</b> ливвиковского младописьменного варианта, которые
            в форме номинатива ед. заканчиваются на <b>{{$num==1 ? 'u или a' : 'y или ä'}}</b>, при этом в форме генитива ед. заканчиваются на <b>{{$num==1 ? 'a' : 'ä'}}n</b>,
            a <b>{{$part_gr_name}}</b>
</p>

<table class="table-bordered">
    <tr>
        <th>N</th>
        <th>номинатив ед.ч.</th>
        <th>генитив ед.ч.</th>
        <th>партитив мн.ч.</th>
        
        @foreach ($words as $lemma_id => $lemma)
        <tr style="text-align: right">
            <td>{{$count++}}</td>
            <td><a href="/dict/lemma/{{$lemma_id}}">{{$lemma['nom']}}</a></td>
            <td>{{$lemma['gen']}}</td>
            <td>{{$lemma['part']}}</td>
        </tr>
        @endforeach
    </tr>
</table>
@endsection