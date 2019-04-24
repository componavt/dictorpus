<?php $count = 1 ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('body')
    @foreach($lemmas as $pos_name=>$lemmas_pos)
    <h3>{{$pos_name}}</h3>
    <table class="table">
        <tr>
            <th>No</th>
            <th>лемма</th>
            <th>генетив ед.ч.</th>
            <th>иллатив ед.ч.</th>
            <th>терминатив ед.ч.</th>
            <th>аддитив ед.ч.</th>
        </tr>
        @foreach($lemmas_pos as $lemma_id=>$lemma)
        <tr>
            <td>{{$count++}}</td>
            <td>{{$lemma['lemma']}}</td>
            <td>{{$lemma['gen_sg']}}</td>
            <td>
                @if($lemma['ill_sg']['old'])
                {{$lemma['ill_sg']['old']}}<br>
                @endif
                @if($lemma['ill_sg']['new'] && ($lemma['ill_sg']['old'] != $lemma['ill_sg']['new'])) 
                <span style="color:red">{{$lemma['ill_sg']['new']}}</span>
                @endif
            </td>
            <td>
                @if($lemma['term_sg']['old'])
                {{$lemma['term_sg']['old']}}<br>
                @endif
                @if($lemma['term_sg']['new'] && ($lemma['term_sg']['old'] != $lemma['term_sg']['new'])) 
                <span style="color:red">{{$lemma['term_sg']['new']}}</span>
                @endif
            </td>
            <td>
                @if($lemma['add_sg']['old'])
                {{$lemma['add_sg']['old']}}<br>
                @endif
                @if($lemma['add_sg']['new'] && ($lemma['add_sg']['old'] != $lemma['add_sg']['new'])) 
                <span style="color:red">{{$lemma['add_sg']['new']}}</span>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    @endforeach
@stop

