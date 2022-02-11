<?php $list_count=1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemma_frequency') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        <p>{{trans('dict.lemma_frequency_comment')}}</p>
        @include('corpus.text.frequency._search_lemma_form',['url' => '/corpus/text/frequency/lemmas']) 

        @if ($lemmas)
        <table id="lemmasTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('messages.frequency') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma_id => $lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lemma') }}">
                    <a href="{{ LaravelLocalization::localizeURL("/dict/lemma/".$lemma_id) }}">
                        {{$lemma['lemma']}}
                    </a>
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                        {{$lemma['pos_name']}}
                </td>
                <td data-th="{{ trans('messages.frequency') }}">
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text?frequency='
                       .$lemma['frequency'].'search_lang%5B%5D='.$url_args['search_lang']
                       .'&search_lemma='.$lemma['lemma']) }}">
                      {{$lemma['frequency']}}
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
    </div>
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/1.11.3/js/dataTables.numericCommaSort.js"></script>
    <script src="//cdn.datatables.net/1.11.3/js/dataTables.numericCommaTypeDetect.js"></script>
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    selectWithLang('.select-dialect', "/dict/dialect/list", 'search_lang', '', true);
    
    $('#lemmasTable').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/ru.json'
        },
        "order": [[ 3, "desc" ]]
    } );
@stop

