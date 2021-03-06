<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.text_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/table.css')!!}
    {!!Html::style('css/buttons.css')!!}
@stop

@section('body')
<div class="row">
    <div class="col-sm-6 col-md-5 col-lg-4">
        <p>
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
            | <a href="{{ LaravelLocalization::localizeURL('/help/text/search') }}">? {{ trans('navigation.help') }}</a>
        </p>
    </div>
    <div class="col-sm-6 col-md-7 col-lg-8">
        <p class="comment" style="text-align: right">{!!trans('messages.search_comment')!!}</p>
    </div>
</div>
        
        @include('widgets.modal',['name'=>'modalHelp',
                                  'title'=>trans('navigation.help'),
                                  'modal_view'=>'help.text._search'])
                                  
        @include('corpus.text._search_form') 

        @include('widgets.founded_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table-bordered table-striped table-wide rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.dialect') }}</th>
                <th>{{ trans('corpus.corpus') }}</th>
                <th>{{ trans('corpus.genre') }}</th>
                <th>{{ trans('corpus.title') }}</th>
                @if (!$url_args['search_word'])
                <th>{{ trans('messages.translation') }}</th>
                @else
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
                <td data-th="{{ trans('corpus.genre') }}">{{$text->genresToString()}}</td>
                <td data-th="{{ trans('corpus.title') }}">
                    {{ $text->authorsToString() ? $text->authorsToString().'.' : '' }}
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}{{$args_by_get}}">{{$text->title}}</a>
                @if ($url_args['search_word'] && $text->transtext)
                    <br>({{$text->transtext->title}})
                @endif
                </td>
                @if (!$url_args['search_word'])
                <td data-th="{{ trans('messages.translation') }}">
                    @if ($text->transtext)
                    {{ $text->transtext->authorsToString() ? $text->transtext->authorsToString().'.' : '' }}
                    {{$text->transtext->title}}
                    @endif
                </td>
                @else
                <td data-th="{{ trans('corpus.sentences') }}">                    
                    @foreach ($text->sentencesFromText($url_args['search_word']) as $sentence_id => $sentence)
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
                             'route' => 'text.destroy', 
                             'args'=>['id' => $text->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $texts->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/search.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    toggleSearchForm();
    recDelete('{{ trans('messages.confirm_delete') }}');
    $(".multiple-select-lang").select2();
    $(".multiple-select-corpus").select2();
    $(".multiple-select-genre").select2({
        width: '100%',
        ajax: {
          url: "/corpus/genre/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              corpus_id: selectedValuesToURL("#search_corpus")
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
