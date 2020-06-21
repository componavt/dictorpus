@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    <h3>Импорт</h3>
    <p><a href="import/concept_parser">Импорт понятий из СОСД</a></p>
    <p><a href="import/concept_parser_check">Проверка понятий из СОС</a></p>
    <p><a href="import/dict_parser?search_lang=1">Парсер вепсского словаря</a>
    
    <h3>Экспорт</h3>
    <p><a href="export/unimorph">Экспорт словоформ в Unimorph</a>    </p>
    <ul>
    @foreach ($langs as $l_id=>$l_name)
        <li><a href="export/unimorph?search_lang={{$l_id}}">{{$l_name}}</a></li>
    @endforeach
    </ul>
    
    <p><a href="export/compounds_for_unimorph">Экспорт фразеологизмов в Unimorph</a></p>

    <p><a href="export/bible">Экспорт вепсской Библии</a></p>
    
    <h3>Проверить</h3>
    <p>окончания у словоформ по флективным правилам</p>
    <ul>
    @foreach ($langs as $l_id=>$l_name)
        <li>
            <a href="service/check_wordforms_by_rules?search_lang={{$l_id}}">{{$l_name}}</a></li>
    @endforeach
    </ul>
    
    <h3>Исправить данные</h3>
    <p><a href="service/add_wordform_affixes">Добавить аффиксы словоформам</a> (с грамсетами и неаналитические формы (без пробелов))</p>
    <p><a href="service/add_unmarked_links">Добавить связи текст-словарь</a></p>
    <p><a href="service/generate_wordforms">Сгенерировать словоформы по имеющимся</a></p>
    <p><a href="service/calculate_lemma_wordforms">Записать у лемм количество словоформ</a> ({{$count_lemmas_without_wordform_total}})</p>
    
@endsection