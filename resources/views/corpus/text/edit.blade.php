@extends('layouts.master')

@section('title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.texts') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_text')}} "{{ $text->title}}"</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($text, array('method'=>'PUT', 'route' => array('text.update', $text->id))) !!}
        @include('corpus.text._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit',
                                      'lang_values' => $lang_values, 
                                      'corpus_values'  => $corpus_values])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    $(".multiple-select").select2();
    
    $(".multiple-select-dialect").select2({
        width: '100%',
        ajax: {
          url: "/corpus/text/dialect_list",
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
    
    $('.text-unlock').click(function() {
        $(this).hide();
        $('#text').prop('readonly',false);
    });
@stop
