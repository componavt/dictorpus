@extends('layouts.page')

@section('page_title')
    {{trans('dict.search_'.$property)}}
@stop

@section('body')     
    <h2>Оценка результатов</h2>
    <p><b>Язык:</b> {{$search_lang_name}}</p>
    
    <h3>Поиск по конечным буквосочетаниям</h3>
    @include('experiments.results_search_one', ['results'=>$results[0]])
    
    @if (isset($results[1]))
        <h3>Поиск по псевдоокончаниям</h3>
        @include('experiments.results_search_one', ['results'=>$results[1]])
    @endif
    
    @if (isset($results[2]))
        <h3>Распределение длин конечного буквосочетания</h3>
        <table class="table-bordered">
            <tr>
                <th>Length</th>
                @foreach ($results[2]['len_list'] as $l)
                <th>{{$l}}</th>
                @endforeach
            </tr>
            @foreach ($results[2]['p_list'] as $p_name => $p_info)
            <tr>
                <th>{{$p_name}}</th>
                <?php $max = max($p_info);?>
                @foreach ($p_info as $len => $count)
                <td<?php print $count==$max ? ' style="color: red; fonr-weight: bold;"' : ''; ?>>
                    {{$count}}</td>
                @endforeach            
            </tr>
            @endforeach            
        </table>
        
        <div id="ValuationChart">
            {!! $results[2]['chart']->container() !!}
        </div>
        {!! $results[2]['chart']->script() !!}
    @endif

@stop

@section('footScriptExtra')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
@stop


