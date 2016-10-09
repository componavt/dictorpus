@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.lemmas') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_lemma')}}: {{ $lemma->lemma}}</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($lemma, array('method'=>'PUT', 'route' => array('lemma.update', $lemma->id))) !!}
        @include('dict.lemma._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit',
                                      'lang_values' => $lang_values, 
                                      'pos_values'  => $pos_values])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
@stop

@section('jqueryFunc')
    $(".multiple-select-relation").select2({
        ajax: {
          url: "/dict/lemma/meanings_list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              lang_id: $( "#lemma_lang_id option:selected" ).val() {{-- $lemma->lang_id--}},
              pos_id: $( "#lemma_pos_id option:selected" ).val(), {{-- $lemma->pos_id --}}
              lemma_id: {{ $lemma->id}}
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
    
    @foreach ($langs_for_meaning as $lang_id => $lang_text)
        @if ($lang_id != $lemma->lang_id)
            $(".multiple-select-translation-{{ $lang_id }}").select2({
                ajax: {
                  url: "/dict/lemma/meanings_list",
                  dataType: 'json',
                  delay: 250,
                  data: function (params) {
                    return {
                      q: params.term, // search term
                      lang_id: {{ $lang_id }},
                      pos_id: $( "#lemma_pos_id option:selected" ).val(),
                      lemma_id: {{ $lemma->id}}
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
        @endif
    @endforeach
@stop
