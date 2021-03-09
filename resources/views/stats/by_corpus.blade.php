@extends('layouts.page')

@section('page_title')
{{ trans('stats.stats_by_corpus') }}
@endsection

@section('headExtra')
    {!!Html::style('css/stats_by_corp.css')!!}
@stop

@section('body')
    <div id="CorpusNumByLangChart" style="margin-bottom: 20px;">
        {!! $chart->container() !!}
    </div>
    {!! $chart->script() !!}
    
        <table class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>{{ trans('corpus.corpus') }}</th>
                @foreach (array_keys($lang_corpuses) as $lang_name)
                <th>{{$lang_name}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($corpus_langs as $corpus_name => $lang_num)
            <tr>
                <td>{{$corpus_name}}</td>
                @foreach(array_values($lang_num) as $num)
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

