@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/lemma.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.lemmas') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_lemma')}}: {{ $lemma->lemma}}</h2>
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
            @if (User::checkAccess('dict.edit'))
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">{{ trans('messages.create_new_f') }}</a>
            @endif
        </p>
        
        {!! Form::model($lemma, array('method'=>'PUT', 'route' => array('lemma.update', $lemma->id))) !!}
        @include('dict.lemma._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit',
                                      'lang_values' => $lang_values, 
                                      'pos_values'  => $pos_values])
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
    
    $(".add-new-relation").click(function(){
        var meaning_id = $(this).attr("data-for");
        var relation = $('#new_relation_' + meaning_id + ' option:selected');
        var relation_id = relation.val();
        $('#relation_'+meaning_id + '_' + relation_id).show('slow');
        relation.remove();
    });
    
    $(".multiple-select-relation").select2({
        width: 'resolve',
        ajax: {
          url: "/dict/lemma/meanings_list",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              lang_id: $( "#lemma_lang_id option:selected" ).val(),
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
    
    $('.empty-relation').css('display','none');
    
    @foreach ($langs_for_meaning as $lang_id => $lang_text)
        @if ($lang_id != $lemma->lang_id)
            $(".multiple-select-translation-{{ $lang_id }}").select2({
                width: '100%',
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
