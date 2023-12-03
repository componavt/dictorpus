@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Определение диалектной принадлежности</h2>
    <h3>Проверка результатов эксперимента</h3>
    
    <p>Распределение правильно/ошибочно наречия карельского языка, т.е. насколько правильно был соотнесен диалект с точки зрения его принадлежности к наречию.</p>
    <div style="margin-bottom: 20px;">
    {!! $charts['langs']->container() !!}
    </div>
    {!! $charts['langs']->script() !!}
    
    <p>Графики показывают соотношение текстов, для которых был правильно или ошибочно определен диалект. 
       По оси X - соотношение в % к общему числу текстов этого диалекта. 
       В скобках рядом с именем - общее количество текстов этого диалекта.
    
    @foreach ($langs as $lang)
    <h4>{{ $lang->name }}</h4>
    <p>Правильно были определены {{ round(100*sizeof($stats[$lang->id]['texts']['right'])/$stats[$lang->id]['total'], 2) }}% текстов,
    ошибочно - {{ round(100*sizeof($stats[$lang->id]['texts']['wrong'])/$stats[$lang->id]['total'], 2) }}% текстов.</p> 
   
    
    <div style="margin-bottom: 20px;">
    {!! $charts[$lang->id]->container() !!}
    </div>
    {!! $charts[$lang->id]->script() !!}

    @endforeach
@endsection

@section('footScriptExtra')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
@stop
