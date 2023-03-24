@extends('layouts.page')

@section('page_title')
{{ trans('search.search_simple_title_by_corpus') }}
@stop

@section('body')
        @include('_form_simple_search', ['route'=>'text.simple_search', 'search_w'=>$url_args['search_w']])

        @include('corpus.text.search._simple_found_records', 
            ['text_total'=>$numAll,
            'count'=>'<b>'.format_number($numAll).'</b>'])

        {!! trans('search.search_simple_text') !!}
        
        @include('corpus.text.search._texts_results') 
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop
