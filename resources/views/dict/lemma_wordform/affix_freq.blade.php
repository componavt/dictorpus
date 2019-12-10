<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.affix_freq') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        @include('dict.lemma_wordform._search_form',['url' => '/dict/lemma_wordform/affix_freq']) 

        @if ($lemmas)
        <table id="affixTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                @if (!$url_args['search_pos'])
                <th>{{ trans('dict.pos') }}</th>
                @endif
                <th>{{ trans('dict.gramsets') }}</th>
                <th>{{ trans('dict.affixes') }}</th>
                <th>{{ trans('dict.right_sort') }}</th>
                <th>{{ trans('messages.frequency') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                @if (!$url_args['search_pos'])
                <td data-th="{{ trans('dict.pos') }}">{{$lemma->pos->name}}</td>
                @endif
                <td data-th="{{ trans('dict.gramsets') }}">{{\App\Models\Dict\Gramset::getStringByID($lemma->gramset_id)}}</td>
                <td data-th="{{ trans('dict.affixes') }}" style="text-align: right">{{$lemma->affix}}</td>
                <td data-th="{{ trans('dict.right_sort') }}" style="color: white">{{$lemma->reverse_affix}}</td>
                <td data-th="{{ trans('messages.frequency') }}" style="text-align: right">
                    <a href="{{ LaravelLocalization::localizeURL('/dict/wordform/?search_lang=').$url_args['search_lang']
                                ."&search_pos=".$lemma->pos_id."&search_gramset=".$lemma->gramset_id."&search_affix=".urlencode($lemma->affix) }}">{{$lemma->frequency}}</a>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
    </div>
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
@stop

@section('jqueryFunc')
    $(document).ready( function () {
        $('#affixTable').DataTable();
    } );
@stop
