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
            <li><a href="/service/correct/generate_wordforms?search_lang=5&search_pos={{$pos_code}}&w_count={{$i}}">с {{$i}} словоформами</a></li>
            @endfor
        </ul>
        @endforeach
    </ul>

    <h2>Список ливвиковских словоформ</h2>
    <ul>
        @foreach($counts as $pos_id=>$pos_count)
        <li>{{\App\Models\Dict\PartOfSpeech::getNameById($pos_id)}}</li>
        <ul>
            @foreach($pos_count as $w_count=>$count)
            <li><a href="/service/correct/wordforms_by_wordform_total?search_lang=5&search_pos={{$pos_id}}&w_count={{$w_count}}">с {{$w_count}} словоформами</a> ({{$count}})</li>
            @endforeach
        </ul>
        @endforeach
    </ul>
    
@endsection