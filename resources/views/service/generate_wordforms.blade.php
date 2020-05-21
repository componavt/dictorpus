@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    <h2>Сгенерировать словоформы по имеющимся</h2>
    <ul>
        @foreach($pos_list as $pos_code => $pos_name)
        <li>ливвиковские {{$pos_name}}</li>
        <ul>
            @for($i=4; $i<6; $i++)
            <li><a href="/service/generate_wordforms?search_lang=5&search_pos={{$pos_code}}&w_count={{$i}}">с {{$i}} словоформами</a></li>
            @endfor
        </ul>
        @endforeach
    </ul>

    <h2>Список словоформ</h2>
    <ul>
        @foreach($pos_list as $pos_code => $pos_name)
        <li>ливвиковские {{$pos_name}}</li>
        <ul>
            @foreach($counts as $i=>$count)
            <li><a href="/service/generate_wordforms?search_lang=5&search_pos={{$pos_code}}&w_count={{$i}}">с {{$i}} словоформами</a> ({{$count}})</li>
            @endfor
        </ul>
        @endforeach
    </ul>
    
@endsection