@extends('layouts.page')

@section('page_title')
{{ trans('corpus.gram_search') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/table.css')!!}
    {!!Html::style('css/buttons.css')!!}
@stop

@section('body')
        <p>{!!trans('messages.search_comment')!!}</p>
        
        @include('widgets.modal',['name'=>'modalHelp',
                                  'title'=>trans('navigation.help'),
                                  'modal_view'=>'help.text._search'])
                                  
        {!! Form::open(['url' => '/corpus/sentence/results', 
                             'method' => 'get', 'target' => '_blank']) 
        !!}
<div class="show-search-form">{{trans('messages.advanced_search')}} &#8595;</div>
<div class="search-form search-text">        
<div class="row">
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_lang', 
                 'values' => $lang_values,
                 'value' => $url_args['search_lang'],
                 'title' => trans('dict.lang'),
                 'class'=>'multiple-select-lang form-control',
        ])                 
    </div>
    <div class="col-md-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_corpus', 
                 'values' => $corpus_values,
                 'value' => $url_args['search_corpus'],
                 'title' => trans('corpus.corpus'),
                 'class'=>'multiple-select-corpus form-control'
            ])
    </div>
    <div class="col-md-4{{sizeof($url_args['search_dialect']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_dialect', 
                 'values' =>$dialect_values,
                 'value' => $url_args['search_dialect'],
                 'title' => trans('dict.dialect'),
                 'class'=>'multiple-select-dialect form-control'
            ])
    </div>
    <div class="col-md-4{{sizeof($url_args['search_genre']) ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._select2', 
                ['name' => 'search_genre', 
                 'values' => $genre_values,
                 'value' => $url_args['search_genre'],
                 'title' => trans('corpus.genre'),
                 'class'=>'multiple-select-genre form-control'
        ])                 
    </div>
    <div class="col-md-2{{$url_args['search_year_from'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_year_from', 
                 'value' => $url_args['search_year_from'] ? $url_args['search_year_from'] : '',
                 'title' => trans('messages.year_from')
                ])                               
    </div>
    <div class="col-md-2{{$url_args['search_year_to'] ? '' : ' ext-form'}}">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_year_to', 
                 'help_func' => 'callHelpYear()',
                 'value' => $url_args['search_year_to'] ? $url_args['search_year_to'] : '',
                 'title' => trans('messages.year_to')
                ])                               
    </div>
    <div class="col-md-4 search-button-b">       
        <span>
        {{trans('messages.show_by')}}
        </span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>                 
</div>
<div class="hide-search-form">{{trans('messages.simple_search')}} &#8593;</div>

<div class="row">
    <div class="col-md-4">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_word1', 
                 'special_symbol' => true,
                 'value' => $url_args['search_word1'],
                 'title'=> trans('corpus.word')
                ])
                               
    </div>
</div>
{!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/search.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    toggleSearchForm();
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
