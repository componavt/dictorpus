<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('corpus.text_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('content')
        <h2>{{ trans('corpus.text_list') }}</h2>
        
        <p style="text-align:right">
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/corpus/text/', 
                             'method' => 'get']) 
        !!}
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form._formitem_select2', 
                ['name' => 'search_lang', 
                 'values' => $lang_values,
                 'value' => $url_args['search_lang'],
                 'title' => trans('dict.lang'),
                 'class'=>'multiple-select-lang form-control'
        ])
                 
    </div>
    <div class="col-sm-4">
        @include('widgets.form._formitem_select2',
                ['name' => 'search_dialect', 
                 'values' =>$dialect_values,
                 'value' => $url_args['search_dialect'],
                 'title' => trans('dict.dialect'),
                 'class'=>'multiple-select-dialect form-control'
            ])
    </div>
    <div class="col-sm-4">
        @include('widgets.form._formitem_select2', 
                ['name' => 'search_corpus', 
                 'values' => $corpus_values,
                 'value' => $url_args['search_corpus'],
                 'title' => trans('corpus.corpus'),
                 'class'=>'multiple-select-corpus form-control'
            ])
    </div>
</div>                 
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form._formitem_text', 
                ['name' => 'search_title', 
                 'special_symbol' => true,
                'value' => $url_args['search_title'],
                'attributes'=>['placeholder' => trans('corpus.title')]])
                               
    </div>
    <div class="col-sm-3">
        @include('widgets.form._formitem_text', 
                ['name' => 'search_word', 
                 'special_symbol' => true,
                'value' => $url_args['search_word'],
                'attributes'=>['placeholder' => trans('corpus.word')]])
                               
    </div>
    <div class="col-sm-2">
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
    </div>
    <div class="col-sm-1" style='text-align:right'>       
        {{trans('messages.show_by')}}
    </div>
    <div class="col-sm-1">
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['placeholder' => trans('messages.limit_num') ]]) 
    </div>
    <div class="col-sm-1">
                {{ trans('messages.records') }}
    </div>
</div>                 
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.dialect') }}</th>
                <th>{{ trans('corpus.corpus') }}</th>
                <th>{{ trans('corpus.title') }}</th>
                <th>{{ trans('messages.translation') }}</th>
                @if ($url_args['search_word'])
                <th style='text-align: center'>{{ trans('corpus.sentences') }}</th>
                @endif
                @if (User::checkAccess('corpus.edit'))
                <th colspan="2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($texts as $text)
            <tr>
                <td>{{ $list_count++ }}</td>
                <td>{{$text->lang->name}}</td>
                <td>
                    @if($text->dialects)
                        @foreach ($text->dialects as $dialect)
                        {{$dialect->name}}<br>
                        @endforeach
                        
                    @endif
                </td>
                <td>{{$text->corpus->name}}</td>
                <td><a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}{{$args_by_get}}">{{$text->title}}</td>
                <td>
                    @if ($text->transtext)
                    {{$text->transtext->title}}
                    @endif
                </td>
                
                @if ($url_args['search_word'])
                <td>
                    @foreach ($text->sentences($url_args['search_word']) as $sentence_id => $sentence)
                    <ol start="{{$sentence_id}}">
                        <li>@include('corpus.text.show_sentence',['count'=>$sentence_id])</li>
                    </ol>
                    @endforeach
                </td>
                @endif
                
                @if (User::checkAccess('corpus.edit'))
                <td>
                    @include('widgets.form._button_edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/text/'.$text->id.'/edit'])
                 </td>
                <td>
                    @include('widgets.form._button_delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             $route = 'text.destroy', 
                             'id' => $text->id])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $texts->appends($url_args)->render() !!}
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')

    toggleSpecial();
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/text');
    $(".multiple-select-lang").select2();
    $(".multiple-select-corpus").select2();
    
    $(".multiple-select-dialect").select2({
        width: '100%',
        ajax: {
          url: "/dict/dialect/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              lang_id: selectedValuesToURL("#search_lang")
            };
          },
          processResults: function (data) {
            return {
              results: data
            };
          },          
          cache: true
        }
    });
        
        
@stop
