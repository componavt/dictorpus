<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.multidict') }}
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/essential_audio.css')!!}
    {!!Html::style('css/essential_audio_circle.css')!!}
    {!!Html::style('css/essential_audio_circle_mini.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        @include('service.dict.multi._search_form',['url' => '/service/dict/multi']) 
        @include('widgets.found_records', ['numAll'=>$numAll])

        @if ($lemmas)
        <table id="lemmasTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th></th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('navigation.concepts') }}</th>
                <!--th>{{ trans('messages.frequency') }}</th-->
                <th>{{ trans('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma)
            <tr id="row-{{$lemma->id}}">
                <td data-th="No">{{ $list_count++ }}</td>
                <td>
                @foreach ($lemma->getAudioUrls() as $audio_url)
                        @include('widgets.audio_decor', ['route'=>$audio_url])
                @endforeach
                </td>
                <td data-th="{{ trans('dict.lemma') }}">
                    <a href="{{ LaravelLocalization::localizeURL("/dict/lemma/".$lemma->id) }}">
                        {{$lemma->lemma}}
                    </a>
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                    {{$lemma->pos->name}}
                </td>
                <td data-th="{{ trans('navigation.concepts') }}">
                    {{$lemma->conceptNames()}}
                </td>
{{--                <td data-th="{{ trans('messages.frequency') }}">
                      {{$lemma['frequency']}}
                </td> --}}
                <td data-th="{{ trans('messages.actions') }}" style="text-align:center">
                    <a class="set-status status{{$lemma->labelStatus($label_id)}}" id="status-{{$lemma->id}}" 
                       onClick="setStatus({{$lemma->id}}, {{$label_id}})"
                       data-old="{{$lemma->labelStatus($label_id)}}" 
                       data-new="{{$lemma->labelStatus($label_id) ? 0 : 1}}"></a>
                    <i class="fa fa-trash fa-lg remove-label" onClick="removeLabel({{$lemma->id}}, {{$label_id}})" title="Удалить из списка"></i>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
            {!! $lemmas->appends($url_args)->render() !!}
        @endif
    </div>
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.11.4/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/sorting/numeric-comma.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.11.4/type-detection/numeric-comma.js"></script>
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/essential_audio.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/lemma.js')!!}
@stop

@section('jqueryFunc')
    selectWithLang('.select-dialect', "/dict/dialect/list", 'search_lang', '', true);    
@stop

