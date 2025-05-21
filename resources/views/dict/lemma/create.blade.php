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
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
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

        {!! Form::open(array('method'=>'POST', 'route' => ['lemma.store'])) !!}
        @include('dict.lemma.form._create_edit', ['submit_title' => trans('messages.create_new_f'),
                                      'action' => 'create',
                                      'lemma_value' => '',
                                      'dialects_value' => [],
                                      'obj' => NULL])
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
    selectWithLang('.select-wordform-dialect', "/dict/dialect/list", 'lang_id', '{{ trans('dict.select_dialect') }}');
    selectWithLang('.select-dialects', "/dict/dialect/list", 'lang_id', '{{ trans('dict.select_dialect') }}');
    selectPhrase();
    selectVariants();
@stop
