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
        <a href="{{ LaravelLocalization::localizeURL('/corpus/sentence') }}">{{ trans('corpus.gram_search') }}</a> |
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
            | <a href="{{ LaravelLocalization::localizeURL('/help/text/search') }}">? {{ trans('navigation.help') }}</a>
        </p>
        
        @include('widgets.modal',['name'=>'modalHelp',
                                  'title'=>trans('navigation.help'),
                                  'modal_view'=>'help.text._search'])
                                  
        @include('corpus.text.form._search', ['form_url'=> '/corpus/text/', 'full'=>true]) 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @include('corpus.text.search._texts_results') 
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/search.js')!!}
    {!!Html::script('js/help.js')!!}
    {!!Html::script('js/form.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    toggleSearchForm();
    recDelete('{{ trans('messages.confirm_delete') }}');
    $(".multiple-select-lang").select2();
    $(".multiple-select-corpus").select2();
    selectGenre();
    selectWithLang('.multiple-select-dialect', "/dict/dialect/list", 'search_lang', '', true);
    selectPlot('.multiple-select-plot', 'search_genre');
    selectTopic('search_plot');
    selectGenre();
    selectDistrict();
    selectEventPlace();
    selectPlace();
    selectBirthDistrict();
    selectBirthPlace();
@stop
