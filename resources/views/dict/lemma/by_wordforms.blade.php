@extends('layouts.page')

@section('page_title')
{{ trans('dict.search_lemmas_by_wordforms') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')   
        @include('widgets.modal',['name'=>'modalHelp',
                                  'title'=>trans('navigation.help'),
                                  'modal_view'=>'help.lemma._search'])
                                  
<div class="row">
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}">{{ trans('search.advanced_search') }}</a> 
            |
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif

        </p>

        @include('dict.lemma.search._by_wordforms_form',['url' => '/dict/lemma/']) 

        @include('widgets.found_records', ['numAll'=>$numAll])

        @include('dict.lemma.search._lemmas_results') 
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/search.js')!!}
    {!!Html::script('js/help.js')!!}
    {!!Html::script('js/lemma.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    toggleSearchForm();
    recDelete('{{ trans('messages.confirm_delete') }}');
    
    selectWithLang('.select-dialects', "/dict/dialect/list", 'search_lang', '{{ trans('dict.dialect') }}');
    selectGramset('search_lang', 'search_pos', '{{ trans('dict.gramset_for_wordform') }}', true);
@stop


