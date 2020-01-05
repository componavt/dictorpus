@extends('layouts.page')

@section('page_title')
Поиск части речи и грамсетов по аналогии среди уникальных словоформ (вместе с аналитическими формами)
@stop

@section('body')     
    <h2>Оценка результатов</h2>
    <p><b>Язык:</b> {{$search_lang_name}}</p>
    
    <h3>С аналитическими словоформами</h3>
    @include('experiments.search_pos_gramsets_by_unique_wordforms_results_add', ['results'=>$results[0]])

    <h3>Без аналитических словоформ</h3>
    @include('experiments.search_pos_gramsets_by_unique_wordforms_results_add', ['results'=>$results[1]])
@stop

@section('footScriptExtra')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
@stop


