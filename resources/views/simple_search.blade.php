@extends('layouts.page')

@section('page_title')
{{ trans('search.search_simple_title') }}
@stop

@section('body')
        @include('_form_simple_search', ['route'=>'simple_search'])

        @include('dict.lemma.search._simple_found_records', 
            ['count'=>'<a href="'.route('lemma.simple_search',['search_w'=>$search_w]).'"><b>'.format_number($lemma_total).'</b></a>'])
        @include('corpus.text.search._simple_found_records', 
            ['count'=>'<a href="'.route('text.simple_search',['search_w'=>$search_w]).'"><b>'.format_number($text_total).'</b></a>'])

@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop
