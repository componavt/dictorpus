<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('corpus.text_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('content')
        <h1>{{ trans('corpus.text_list') }}</h1>
        
        <p style="text-align:right">
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        @include('corpus.text._search_form',['url' => '/corpus/text/']) 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table-bordered table-striped table-wide rwd-table wide-md">
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
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($texts as $text)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lang') }}">{{$text->lang->name}}</td>
                <td data-th="{{ trans('dict.dialect') }}">
                    @if($text->dialects)
                        @foreach ($text->dialects as $dialect)
                        {{$dialect->name}}<br>
                        @endforeach
                        
                    @endif
                </td>
                <td data-th="{{ trans('corpus.corpus') }}">{{$text->corpus->name}}</td>
                <td data-th="{{ trans('corpus.title') }}"><a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}{{$args_by_get}}">{{$text->title}}</a></td>
                <td data-th="{{ trans('messages.translation') }}">
                    @if ($text->transtext)
                    {{$text->transtext->title}}
                    @endif
                </td>
                
                @if ($url_args['search_word'])
                <td data-th="{{ trans('corpus.sentences') }}">
                    @foreach ($text->sentences($url_args['search_word']) as $sentence_id => $sentence)
                    <ol start="{{$sentence_id}}">
                        <li>@include('corpus.text.show_sentence',['count'=>$sentence_id])</li>
                    </ol>
                    @endforeach
                </td>
                @endif
                
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/text/'.$text->id.'/edit'])
                    @include('widgets.form.button._delete', 
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
