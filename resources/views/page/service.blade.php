@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    <h3>Экспорт</h3>
    <p><a href="export/unimorph">Экспорт словоформ в Unimorph</a>
        <ul>
        @foreach ($langs as $l_id=>$l_info)
            <li><a href="export/unimorph?search_lang={{$l_id}}">{{$l_info['name']}}</a></li>
        @endforeach
        </ul>
    </p>
    
    <p><a href="export/compounds_for_unimorph">Экспорт фразеологизмов в Unimorph</a></p>
    
    <h3>Исправить данные</h3>
    <p><a href="service/add_wordform_affixes">Добавить аффиксы словоформам</a> (с грамсетами и не аналитические формы (без пробелов))
        <ul>
        @foreach ($langs as $l_id=>$l_info)
            <li><a href="service/add_wordform_affixes?search_lang={{$l_id}}">{{$l_info['name']}}</a> ({{$l_info['affix_count']}})</li>
        @endforeach
        </ul>
    </p>
@endsection