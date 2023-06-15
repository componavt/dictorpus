<?php $list_count=1; ?>
@extends('layouts.page')

@section('page_title')
Отбор лемм для словаря Зайкова
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        @include('service.dict.zaikov._search_form',['url' => '/service/dict/zaikov/select']) 

        @if ($lemmas)
        <table id="lemmasTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>Выбрать</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma_id=>$lemma)
            <tr id="row-{{$lemma_id}}">
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lemma') }}">
                    <a href="{{ LaravelLocalization::localizeURL("/dict/lemma/".$lemma_id) }}">
                        {{$lemma['lemma']}}
                    </a>
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                    {{$lemma['pos_name']}}
                </td>
                <td data-th="Выбрать">
                    <i class="fa fa-check add-to-list" onClick="addLabel({{$lemma_id}}, {{$label_id}})" title="Добавить в список"></i>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
    </div>
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.4/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/sorting/numeric-comma.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/type-detection/numeric-comma.js"></script>
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/lemma.js')!!}
@stop

@section('jqueryFunc')
    selectWithLang('.select-dialect', "/dict/dialect/list", 'search_lang', '', true);
    
    $('#lemmasTable').DataTable( {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.4/i18n/ru.json'
        },
        "order": [[ 3, "desc" ]]
    } );
@stop

