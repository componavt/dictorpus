@extends('layouts.page')

@section('page_title')
    {{trans('dict.search_'.$property)}}
@stop

@section('body')     
    <h2>Оценка результатов</h2>
    
    <h3>Поиск по самым длинным конечным буквосочетаниям</h3>
    @include('experiments.results_search_all_lang', ['results'=>$results[0]])
    
    @if (isset($results[1])
        <h3>Поиск по самым длинным псевдоокончаниям</h3>
        @include('experiments.results_search_all_lang', ['results'=>$results[1]])
    @endif
@stop

@section('footScriptExtra')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
@stop


