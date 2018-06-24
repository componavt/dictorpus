<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('navigation.relations') }}
@stop

@section('content')
        <h2>{{ trans('navigation.relations') }}</h2>

        @include('dict.lemma.search._relation_form',['url' => '/dict/lemma/relation']) 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($lemmas)
        <table class="table-bordered table-wide table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th colspan="2">{{ trans('dict.lemma') }} 1</th>
                <th>{{ trans('dict.relation') }}</th>
                <th colspan="2">{{ trans('dict.lemma') }} 2</th>
            </tr>
        </thead>
            @foreach($lemmas as $lemma)
            <?php $lemma1 = \App\Models\Dict\Lemma::find($lemma->lemma1_id);
                  $meaning2 = \App\Models\Dict\Meaning::find($lemma->meaning2_id);
                  $lemma2 = $meaning2->lemma; ?>
            <tr>
                <td>{{ $list_count++ }}</td>
                <td>{{ $lemma1->lang->name }}</td>
                <td>{{ $lemma1->pos->name }}</td>
                <td><a href="/dict/lemma/{{$lemma->lemma1_id}}{{$args_by_get}}">{{$lemma->lemma1}}</a></td>
                <td>{{ \App\Models\Dict\Meaning::find($lemma->meaning1_id)->getMultilangMeaningTextsString() }}</td>
                <td>{{ \App\Models\Dict\Relation::find($lemma->relation_id)->name }}</td>
                <td><a href="/dict/lemma/{{$lemma2->id}}{{$args_by_get}}">{{$lemma2->lemma}}</a></td>
                <td>{{ $meaning2->getMultilangMeaningTextsString() }}</td>
            </tr>
            @endforeach
        </table>
            {!! $lemmas->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop