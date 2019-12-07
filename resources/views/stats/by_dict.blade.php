@extends('layouts.page')

@section('page_title')
{{ trans('stats.stats_by_dict') }}
@endsection

@section('headExtra')
    {!!Html::style('css/stats.css')!!}
@stop

@section('body')
    <table class="table-bordered stats-table">
        <tr>
            <td>{{trans('navigation.lemmas')}}</td><td><a href="/dict/lemma">{{$total_lemmas}}</a></td>
        </tr>
        @foreach($lang_lemmas as $lang_name => $lang_num) 
        <tr>
            <td style="text-align: right; font-style:italic">{{$lang_name}}</td><td>{{$lang_num}}</td>
        </tr>
        @endforeach
        <tr>
            <td>{{trans('navigation.wordforms')}}</td><td><a href="/dict/wordform">{{$total_wordforms}}</a></td>
        </tr>
        @foreach($lang_wordforms as $lang_name => $lang_num) 
        <tr>
            <td style="text-align: right; font-style:italic">{{$lang_name}}</td><td>{{$lang_num}}</td>
        </tr>
        @endforeach
        <tr>
            <td>{{trans('stats.meanings')}}</td><td>{{$total_meanings}}</td>
        </tr>
        <tr>
            <td>{{trans('stats.translations')}}</td><td>{{$total_translations}}</td>
        </tr>
        <tr>
            <td>{{trans('stats.relations')}}</td><td><a href="/dict/lemma/relation">{{$total_relations}}</a></td>
        </tr>
    </table>

    <div id="LemmaNumByLangChart" style="margin-top: 20px;">
        {!! $chart->container() !!}
    </div>
    <script src="https://unpkg.com/vue"></script>
    <script>
        var app = new Vue({
            el: '#LemmaNumByLangChart',
        });
    </script>
    <script src=https://cdnjs.cloudflare.com/ajax/libs/echarts/4.0.2/echarts-en.min.js charset=utf-8></script>
    {!! $chart->script() !!}
@stop

@section('footScriptExtra')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
@stop
