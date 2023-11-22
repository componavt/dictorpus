@extends('layouts.page')

@section('page_title')
{{ trans('stats.stats_by_genre') }}
@endsection

@section('body')
    <div id="GenreNumByLangChart" style="margin-bottom: 20px;">
        {!! $chart->container() !!}
    </div>
    {!! $chart->script() !!}
    
        <table class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>{{ trans('corpus.genre') }}</th>
                @foreach (array_keys($lang_genres) as $lang_name)
                <th>{{$lang_name}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($genre_langs as $genre_name => $lang_num)
            <tr>
                <td>{{$genre_name}}</td>
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

