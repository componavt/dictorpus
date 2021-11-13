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
            <th colspan='3'>{{trans('stats.stats_by_words')}}</th>
        </tr>
        <tr>
            <td>{{trans('stats.total_words')}}</td>
            <td colspan='2'>{{$total_words}}</td>
        </tr>
        @foreach($lang_marked['total'] as $lang_name => $lang_num) 
        <tr>
            <td style="text-align: right">{{$lang_name}}</td>
            <td colspan='2'>{{$lang_num}}</td>
        </tr>
        @endforeach
        
        <tr>
            <td>{{trans('stats.total_marked_words')}}</td>
            <td>{{$total_marked_words}}</td>
            <td>{{$marked_words_to_all}} %</td>
        </tr>
        @foreach($lang_marked['marked'] as $lang_name => $lang_num) 
        <tr>
            <td style="text-align: right">{{$lang_name}}</td>
            <td>{{$lang_num}}</td>
            <td>{{$lang_marked['marked%'][$lang_name]}} %</td>
        </tr>
        @endforeach
        
        <tr>
            <td>{{trans('stats.total_checked_words')}}</td>
            <td>{{$total_checked_words}}</td>
            <td>{{$checked_words_to_marked}} %</td>
        </tr>
        @foreach($lang_marked['checked'] as $lang_name => $lang_num) 
        <tr>
            <td style="text-align: right">{{$lang_name}}</td>
            <td>{{$lang_num}}</td>
            <td>{{$lang_marked['checked%'][$lang_name]}} %</td>
        </tr>
        @endforeach

    </table>
@stop
