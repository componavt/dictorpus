<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.zaikovdict') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        @include('widgets.modal',['name'=>'modalAddLemma',
                              'title'=>trans('dict.add-lemma'),
                              'submit_id' => 'save-lemma',                              
                              'submit_title' => trans('messages.save'),
                              'modal_view'=>'dict.lemma.form._create_simple'])
                                  
        @include('service.dict.zaikov._search_form',['url' => '/service/dict/zaikov']) 
        
        <div style="display:flex; justify-content: space-between;">
        @include('widgets.found_records', ['numAll'=>$numAll])
        <a id="call-add-lemma">Создать новую лемму</a>
        </div>
        
        @if ($lemmas)
        <table id="lemmasTable" class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('dict.meanings') }}</th>
                <th>{{ trans('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody id='lemmasRows'>
            @foreach($lemmas as $lemma)
                @include('service.dict.zaikov._row', ['list_count'=>$list_count++])
                
            @endforeach
        </tbody>
        </table>
            {!! $lemmas->appends($url_args)->render() !!}
        @endif
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/new_dict.js')!!}
@stop

@section('jqueryFunc')
    selectWithLang('.select-dialect', "/dict/dialect/list", 'search_lang', '', true);
    addLemma({{$lang_id}}, {{$label_id}});
    posSelect(false);
@stop

