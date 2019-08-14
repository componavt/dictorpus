@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => ['lemma.store'])) !!}
        @include('dict.lemma.form._create_edit', ['submit_title' => trans('messages.create_new_f'),
                                      'action' => 'create',
                                      'lemma_value' => '',
                                      'obj' => NULL])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/meaning.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    addMeaning();
    posSelect();
    langSelect();
    
    $(".select-dialect").select2({
        width: '100%',
        ajax: {
          url: "/dict/dialect/list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              lang_id: $( "#lang_id option:selected" ).val()
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
    
    $(".multiple-select-phrase").select2({
        width: '100%',
        ajax: {
          url: "/dict/lemma/phrase_list",
          dataType: 'json',
          delay: 2500,
          data: function (params) {
            return {
              q: params.term, // search term
              lang_id: $( "#lang_id option:selected" ).val(),
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
