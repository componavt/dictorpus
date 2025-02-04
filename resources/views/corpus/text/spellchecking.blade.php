@extends('layouts.page')

@section('page_title')
{{ trans('navigation.spellchecking') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
        {!! trans('blob.spellchecking') !!}
        @if (User::currentUser())
            @include('widgets.form.formitem._select', 
                    ['name' => 'lang_id', 
                     'values' =>$lang_values,
                     'title' => trans('dict.select_lang'),
                     'attributes' => ['id'=>'lang_id']])
            @include('widgets.form.formitem._textarea', 
                    ['name' => 'text', 
                     'special_symbol' => true,
                     'counter' => 2000,
                     'title'=>trans('corpus.text_for_check'),
                     'attributes'=>['rows'=>20]])
            <input class="btn btn-primary btn-default" type="button" 
                   onclick="spellchecking('{{ LaravelLocalization::getCurrentLocale() }}')" 
                   value="{{trans('messages.check')}}">
        @endif
        <p style='margin-top:20px'><img class="img-loading" id="loading-text" src="{{ asset('images/loading.gif') }}"></p>
        <div id="spellchecking" style="white-space: pre-line; margin-left:12px; margin-right: 12px;"></div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/text.js')!!}
    {!!Html::script('js/form.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
{{-- show/hide a block with lemmas --}}
    showUnlinkedLemmaBlock('{{LaravelLocalization::getCurrentLocale()}}'); 
    limitTextarea("#text");
@stop
