@extends('layouts.page')

@section('page_title')
    {{trans('dict.search_'.$property)}}
@stop

@section('body')     
    <h2>Оценка результатов</h2>
    <p><b>Язык:</b> {{$search_lang_name}}</p>
    
    <h3>Поиск по самым длинным конечным буквосочетаниям</h3>
    @include('experiments.search_by_analog.results_search_one', ['results'=>$results[0]])
    
    @if(isset($results[6]))
        <h4>По группам</h4>
        @include('experiments.search_by_analog.results_search_group', ['results'=>$results[6]])
    @endif
    
   
    @if (isset($results[5]))
        <h3>Поиск по всем конечным буквосочетаниям</h3>
        @include('experiments.search_by_analog.results_search_one', ['results'=>$results[5]])
    @endif
    
    @if (isset($results[1]))
        <h3>Поиск по самым длинным псевдоокончаниям</h3>
        @include('experiments.search_by_analog.results_search_one', ['results'=>$results[1]])
    @endif
    
    @if (isset($results[4]))
        <h3>Поиск по всем псевдоокончаниям</h3>
        @include('experiments.search_by_analog.results_search_one', ['results'=>$results[4]])
    @endif
    
    @if (isset($results[2]))
        <h3>Распределение длин конечного буквосочетания</h3>
        @include('experiments.search_by_analog.end_len_distribution', ['results'=>$results[2]])
    @endif

    @if (isset($results[3]))
        <h3>{{trans('dict.num_shift_error', ['num'=>$results[3]['limit']])}}</h3>
        @include('experiments.search_by_analog.shift_error', ['results'=>$results[3]])
    @endif

@stop

@section('footScriptExtra')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
@stop


