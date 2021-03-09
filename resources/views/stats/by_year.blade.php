@extends('layouts.page')

@section('page_title')
{{ trans('stats.stats_by_year') }}
@endsection

@section('headExtra')
    {!!Html::style('css/stats_by_corp.css')!!}
@stop

@section('body')
    <div id="TextNumByYearChart" style="margin-bottom: 20px;">
        {!! $chart->container() !!}
    </div>
    {!! $chart->script() !!}
    
        <table class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>{{ trans('messages.year') }}</th>
                @foreach (array_keys($label_years) as $label_year)
                <th>{{$label_year}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($text_years as $year => $year_num)
            <tr>
                <td>{{$year}}</td>
                @foreach(array_values($year_num) as $num)
                <td>{{$num}}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
        </table>
@stop

@section('footScriptExtra')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" charset="utf-8"></script>
@stop

