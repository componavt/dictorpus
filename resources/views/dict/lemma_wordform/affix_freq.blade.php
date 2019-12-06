<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.affix_freq') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        @include('dict.lemma_wordform._search_form',['url' => '/dict/lemma_wordform/affix_freq']) 

        @if ($lemmas)
        <table class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('dict.gramsets') }}</th>
                <th>{{ trans('dict.affixes') }}</th>
                <th>{{ trans('messages.frequency') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma)
                @if ($list_count<=$url_args['limit_num'])
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.pos') }}">{{$lemma->pos_id}}</td>
                <td data-th="{{ trans('dict.gramsets') }}">{{$lemma->gramset_id}}</td>
                <td data-th="{{ trans('dict.affixes') }}">{{$lemma->affix}}</td>
                <td data-th="{{ trans('messages.frequency') }}">{{$lemma->frequency}}</td>
            </tr>
                @endif
            @endforeach
        </tbody>
        </table>
        @endif
    </div>
@stop
