@extends('layouts.page')

@section('page_title')
{{ trans('stats.by_corp_markup') }}
@endsection

@section('headExtra')
    {!!Html::style('css/stats_by_corp.css')!!}
@stop

@section('body')
    <table class="table-bordered stats-table" style="margin-bottom: 20px;">
        <tr>
            <th colspan='2'>{{trans('stats.stats_by_words')}}</th>
        </tr>
        <tr>
            <td>{{trans('stats.total_words')}}</td><td>{{$total_words}}</td>
        </tr>
        @foreach($lang_marked['total'] as $lang_name => $lang_num) 
        <tr>
            <td style="text-align: right">{{$lang_name}}</td><td>{{$lang_num}}</td>
        </tr>
        @endforeach
        <tr>
            <td>{{trans('stats.total_marked_words')}}</td><td>{{$total_marked_words}}</td>
        </tr>
        @foreach($lang_marked['marked'] as $lang_name => $lang_num) 
        <tr>
            <td style="text-align: right">{{$lang_name}}</td><td>{{$lang_num}}</td>
        </tr>
        @endforeach
        <tr>
            <td>{{trans('stats.marked_words_to_all')}}</td><td>{{$marked_words_to_all}} %</td>
        </tr>
        @foreach($lang_marked['ratio'] as $lang_name => $lang_num) 
        <tr>
            <td style="text-align: right">{{$lang_name}}</td><td>{{$lang_num}} %</td>
        </tr>
        @endforeach
        
        <tr>
            <td>{{trans('stats.total_checked_words')}}</td><td>{{$total_checked_words}}</td>
        </tr>
        @foreach($lang_marked['checked'] as $lang_name => $lang_num) 
        <tr>
            <td style="text-align: right">{{$lang_name}}</td><td>{{$lang_num}}</td>
        </tr>
        @endforeach
        <tr>
            <td>{{trans('stats.checked_words_to_marked')}}</td><td>{{$checked_words_to_marked}} %</td>
        </tr>

        <tr>
            <th colspan='2'>{{trans('stats.stats_by_examples')}}</th>
        </tr>
        <tr>
            <td>{{trans('stats.total_examples')}}</td><td>{{$total_examples}}</td>
        </tr>
        <tr>
            <td>{{trans('stats.total_checked_examples')}}</td><td>{{$total_checked_examples}}</td>
        </tr>
        <tr>
            <td>{{trans('stats.checked_examples_to_all')}}</td><td>{{$checked_examples_to_all}} %</td>
        </tr>
    </table>
@stop
