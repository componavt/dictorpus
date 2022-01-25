<?php $list_count=1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.multidict') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        @include('service.multidict._search_form',['url' => '/service/multidict']) 

        @if ($lemmas)
        <table id="lemmasTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <!--th>{{ trans('dict.interpretation') }}</th-->
                <th>{{ trans('messages.frequency') }}</th>
                <th>{{ trans('dict.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lemma') }}">
                    <a href="{{ LaravelLocalization::localizeURL("/dict/lemma/".$lemma->lemma_id) }}">
                        {{$lemma->lemma}}
                    </a>
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                        {{$lemma->pos_name}}
                </td>
                <!--td data-th="{{ trans('dict.interpretation') }}">
                    @foreach ($lemma->getMultilangMeaningTexts() as $meaning_string) 
                        {{$meaning_string}}<br>
                    @endforeach
                </td-->
                <td data-th="{{ trans('messages.frequency') }}">
                      {{$lemma->frequency}}
                </td>
                <td data-th="{{ trans('dict.status') }}">
                    <a id="status-{{$lemma->lemma_id}}" class="set-status status{{$lemma->status}}" 
                       onClick="setStatus({{$lemma->lemma_id}})"
                       data-old="{{$lemma->status}}" 
                       data-new="{{$lemma->status ? 0 : 1}}"></a>
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

