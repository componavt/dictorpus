<?php $list_count=1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemma_frequency') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css">
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
                <!--th>{{ trans('dict.interpretation') }}</th-->
                <th>{{ trans('messages.frequency') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma)
            <?php
                $link_to_texts = '/corpus/text?search_lang%5B%5D='.$url_args['search_lang'].'&search_lemma='.$lemma->lemma;
            ?>
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
                    <a href="{{ LaravelLocalization::localizeURL($link_to_texts) }}">
                      {{$lemma->frequency}}
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
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
@stop

@section('jqueryFunc')
    $(document).ready( function () {
        $('#lemmasTable').DataTable( {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/ru.json'
            }
        } );
    } );
@stop

