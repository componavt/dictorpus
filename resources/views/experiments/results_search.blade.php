@extends('layouts.page')

@section('page_title')
    {{trans('dict.search_'.$property)}}
@stop

@section('body')     
    <h2>Оценка результатов</h2>
    <p><b>Язык:</b> {{$search_lang_name}}</p>
    
    @include('experiments.results_search_one', ['results'=>$results[0]])

@stop

@section('footScriptExtra')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
@stop


