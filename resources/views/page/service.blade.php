@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    <h3>Импорт</h3>
    <p><a href="import/concept_parser">Импорт понятий из СОС</a></p>
    <p><a href="import/dict_parser?search_lang=1">Парсер вепсского словаря</a>
    
    <h3>Экспорт</h3>
    <p><a href="export/unimorph">Экспорт словоформ в Unimorph</a>    </p>
    <ul>
    @foreach ($langs as $l_id=>$l_name)
        <li><a href="export/unimorph?search_lang={{$l_id}}">{{$l_name}}</a></li>
    @endforeach
    </ul>
    
    <p><a href="export/compounds_for_unimorph">Экспорт фразеологизмов в Unimorph</a></p>

    <p>Проверить окончания у словоформ по флективным правилам</p>
    <ul>
    @foreach ($langs as $l_id=>$l_info)
        <li>
            <a href="/service/check_wordforms_by_rules?search_lang={{$l_id}}">
                {{$l_info['name']}}
            </a></li>
    @endforeach
    </ul>
    
    <h3><a href="service/correct_data">Исправить данные</a></h3>
@endsection