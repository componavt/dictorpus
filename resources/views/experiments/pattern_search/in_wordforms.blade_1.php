<?php $list_count=1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Поиск закономерностей в анализе словоформ</h2>
    <p><i>{{$lang}}, {{$dialect}}</i></p>
    
    <table class="table-bordered table-wide rwd-table wide-lg">
    @foreach ($patterns as $pattern) 
    <?php $gramsets = \App\Library\Experiments\PatternSearch::getGramsets($dialect_id, $pattern->ending);?>
    <tr>
        <td style="vertical-align:top" rowspan="{{sizeof($gramsets)}}"><b>-{{$pattern->ending}}</b></td>
        @for($i=0; $i<sizeof($gramsets); $i++)
            @if($i>0)
    </tr>
    <tr>
            @endif
        <td>{{App\Models\Dict\PartOfSpeech::getNameById($gramsets[$i]->pos_id)}}</td>
        <td style="vertical-align:top">{{App\Models\Dict\Gramset::getStringById($gramsets[$i]->gramset_id)}}</td>
        <td style="vertical-align:top">{{$gramsets[$i]->count}}</td>
        @endfor
    </tr>
    @endforeach
    </table>
@endsection