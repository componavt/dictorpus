<?php $list_count=1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Поиск закономерностей в формировании словоформ</h2>
    <p><i>{{$lang}}, {{$dialect}}, {{$pos}}</i></p>
    
    <table class="table-bordered table-wide rwd-table wide-lg">
        <tr>
            <th>N</th>
            <th>{{trans('dict.lemma')}}</th>
            <th>-</th>
            <th>+</th>
            <th>{{$gramset}}</th>
            <th>{{trans('messages.total')}}</th>
        </tr>
        @foreach ($types as $m => $m_arr)
            @foreach (collect($m_arr['-'])->sortByDesc('count') as $p => $p_arr)
            <tr>
                <td>{{$list_count++}}</td>
                <td>{{join(', ', $p_arr['lemmas'])}}</td>
                <td style='white-space: nowrap'>{{$m ? '- '.$m : ''}}</td>
                <td style='white-space: nowrap'>{{$p ? '+ '.$p : ''}}</td>
                <td>{{join(', ', $p_arr['wordforms'])}}</td>
                <td>{{$p_arr['count']}}</td>
            </tr>
            @endforeach
        @endforeach
    </table>

@endsection