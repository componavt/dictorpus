@extends('layouts.page')

@section('page_title')
{{ trans('search.search_simple_title_by_dict') }}
@stop

@section('body')
        @include('_form_simple_search', ['route'=>'lemma.simple_search', 
                                         'search_w'=>$url_args['search_w']])
        {!! Form::close() !!}

        @include('dict.lemma.search._simple_found_records', 
            ['lemma_total'=>$numAll,
            'count'=>'<b>'.format_number($numAll).'</b>'])

        {!! trans('search.search_simple_lemma') !!}
        
        @include('dict.lemma.search._lemmas_results') 
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop
