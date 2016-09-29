<?php $list_count = $limit_num * ($page-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('navigation.relations') }}
@stop

@section('content')
        <h2>{{ trans('navigation.relations') }}</h2>

        {!! Form::open(['url' => '/dict/lemma/relation',
                             'method' => 'get',
                             'class' => 'form-inline'])
        !!}
        @include('widgets.form._formitem_text',
                ['name' => 'lemma_name',
                'value' => $lemma_name,
                'attributes'=>['size' => 15,
                               'placeholder'=>trans('dict.lemma')]])
        @include('widgets.form._formitem_select',
                ['name' => 'lang_id',
                 'values' =>$lang_values,
                 'value' =>$lang_id,
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]])
        @include('widgets.form._formitem_select',
                ['name' => 'pos_id',
                 'values' =>$pos_values,
                 'value' =>$pos_id,
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
        @include('widgets.form._formitem_select',
                ['name' => 'relation_id',
                 'values' =>$relation_values,
                 'value' =>$relation_id,
                 'attributes'=>['placeholder' => trans('dict.select_relation') ]]) 
        <br>
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])

        {{trans('messages.show_by')}}
        @include('widgets.form._formitem_text',
                ['name' => 'limit_num',
                'value' => $limit_num,
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) {{ trans('messages.records') }}
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($lemmas)
        <table class="table">
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
                <td><a href="/dict/lemma/{{$lemma->lemma1_id}}">{{$lemma->lemma1}}</a></td>
                <td>{{ \App\Models\Dict\Meaning::find($lemma->meaning1_id)->getMultilangMeaningTextsString() }}</td>
                <td>{{ \App\Models\Dict\Relation::find($lemma->relation_id)->name }}</td>
                <td><a href="/dict/lemma/{{$lemma2->id}}">{{$lemma2->lemma}}</a></td>
                <td>{{ $meaning2->getMultilangMeaningTextsString() }}</td>
            </tr>
            @endforeach
        </table>
            {!! $lemmas->appends(['limit_num' => $limit_num,
                                  'lemma_name' => $lemma_name,
                                  'lang_id'=>$lang_id,
                                  'relation_id'=>$relation_id,
                                  'pos_id'=>$pos_id])->render() !!}
        @endif
@stop
