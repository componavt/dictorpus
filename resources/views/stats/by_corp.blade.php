@extends('layouts.page')

@section('page_title')
{{ trans('stats.stats_by_corp') }}
@endsection

@section('headExtra')
    {!!Html::style('css/stats_by_corp.css')!!}
@stop

@section('body')
    <table class="table-bordered stats-table" style="margin-bottom: 20px;">
        <tr>
            <td>{{trans('navigation.texts')}}</td><td><a href="/corpus/text">{{$total_texts}}</a></td>
        </tr>
        <tr>
            <td>{{trans('navigation.informants')}}</td><td><a href="/corpus/informant">{{$total_informants}}</a></td>
        </tr>
        <tr>
            <td>{{trans('navigation.places')}}</td><td><a href="/corpus/place">{{$total_places}}</a></td>
        </tr>
        <tr>
            <td>{{trans('navigation.recorders')}}</td><td><a href="/corpus/recorder">{{$total_recorders}}</a></td>
        </tr>
    </table>

<p><a href="/stats/by_corp_markup">{{trans('stats.by_corp_markup')}}</a></p>
<p><a href="/stats/by_corpus">{{trans('stats.distribution_by_corpuses')}}</a></p>
<p><a href="/stats/by_genre">{{trans('stats.distribution_by_genres')}}</a></p>
<p><a href="/stats/by_year">{{trans('stats.distribution_by_years')}}</a></p>
@stop
