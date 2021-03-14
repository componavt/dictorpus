<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.relations') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        @include('dict.lemma.search._relation_form',['url' => '/dict/lemma/relation']) 

        @include('widgets.founded_records', ['numAll'=>$numAll])

        @if ($lemmas)
        <table class="table-bordered table-wide table-striped rwd-table wide-lg">
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
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lang') }}">{{ $lemma1->lang->name }}</td>
                <td data-th="{{ trans('dict.pos') }}">{{ $lemma1->pos->name }}</td>
                <td data-th="{{ trans('dict.lemma') }} 1"><a href="/dict/lemma/{{$lemma->lemma1_id}}{{$args_by_get}}">{{$lemma->lemma1}}</a></td>
                <td data-th="{{ trans('dict.interpretation') }}">{{ \App\Models\Dict\Meaning::find($lemma->meaning1_id)->getMultilangMeaningTextsString() }}</td>
                <td data-th="{{ trans('dict.relation') }}">{{ \App\Models\Dict\Relation::find($lemma->relation_id)->name }}</td>
                <td data-th="{{ trans('dict.lemma') }} 2"><a href="/dict/lemma/{{$lemma2->id}}{{$args_by_get}}">{{$lemma2->lemma}}</a></td>
                <td data-th="{{ trans('dict.interpretation') }}">{{ $meaning2->getMultilangMeaningTextsString() }}</td>
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