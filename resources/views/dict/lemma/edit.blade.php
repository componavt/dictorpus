@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/buttons.css')!!}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_lemma')}}: <span class='imp'>{{ $lemma->lemma}}</span></h2>
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
            @if (User::checkAccess('dict.edit'))
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">{{ trans('messages.create_new_f') }}</a>
            @endif
            | <a href="{{ LaravelLocalization::localizeURL('/help/lemma/form') }}">? {{ trans('navigation.help') }}</a>
        </p>

        @include('widgets.modal',['name'=>'modalHelp',
                                  'title'=>trans('navigation.help'),
                                  'modal_view'=>'help.lemma._form'])

        @include('widgets.modal',['name'=>'modalSuggestTemplates',
                                  'title'=>trans('dict.choose_template'),
                                  'modal_view'=>'dict.lemma.choose_template'])

        {!! Form::model($lemma, array('method'=>'PUT', 'route' => array('lemma.update', $lemma->id))) !!}
        @include('dict.lemma.form._create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit',
                                      'lang_id' => null,
				      'pos_id'=>null,
                                      'lang_values' => $lang_values, 
                                      'lemma_value' => $lemma->stemAffixForm(),
                                      'pos_values'  => $pos_values,
                                      'obj' => $lemma->features])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/meaning.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    checkLemmaForm();
    toggleSpecial();
    addMeaning();
    posSelect();
/*    langSelect();*/

    selectWithLang('.select-wordform-dialect', "/dict/dialect/list", 'lang_id', '{{ trans('dict.select_dialect') }}');
    selectWithLang('.select-dialects', "/dict/dialect/list", 'lang_id', '{{ trans('dict.select_dialect') }}');
    selectWithLang('.select-places', "/corpus/place/list", 'lang_id', '{{ trans('dict.meaning_place') }}');
    selectConceptWithoutCategory('pos_id', '{{ trans('dict.select_concept') }}');
    
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
              lang_id: $( "#lang_id option:selected" ).val(),
              pos_id: $( "#pos_id option:selected" ).val(),
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
    
    $(".multiple-select-phrase").select2({
/*        tags: true,*/
/*        allowClear: false,
        tokenSeparators: [' '],
        maximumSelectionLength: 5,*/
        width: '100%',
        ajax: {
          url: "/dict/lemma/list_with_pos_meaning",
          dataType: 'json',
          delay: 250,
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
    
    $(".multiple-select-variants").select2({
        width: '100%',
        ajax: {
          url: "/dict/lemma/list_with_pos_meaning",
          dataType: 'json',
          delay: 250,
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
                      pos_id: $( "#pos_id option:selected" ).val(),
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
