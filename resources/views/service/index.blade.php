@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    @if (User::checkAccess('admin'))
    <h3>Импорт</h3>
    <p><a href="import/concept_parser">Импорт понятий из СОСД</a></p>
    <p><a href="import/concept_parser_check">Проверка понятий из СОС</a></p>
    <p><a href="import/dict_parser?search_lang=1">Парсер вепсского словаря</a>
    <p><a href="import/dict_zaikov_verb_parser">Парсер глаголов из словаря Зайкова</a>
    
    <h3>Экспорт</h3>
    <p><a href="export/unimorph">Экспорт словоформ в Unimorph</a>    </p>
    <ul>
    @foreach ($langs as $l_id=>$l_name)
        <li><a href="export/unimorph?search_lang={{$l_id}}">{{$l_name}}</a></li>
    @endforeach
    </ul>
    
    <p><a href="export/compounds_for_unimorph">Экспорт фразеологизмов в Unimorph</a></p>
    <p><a href="export/bible">Экспорт вепсской Библии</a></p>
    <p><a href="export/for_mobile">Экспорт данных для мобильной версии</a></p>
    
    <h3>Проверить</h3>
    <p>окончания у словоформ по флективным правилам</p>
    <ul>
    @foreach ($langs as $l_id=>$l_name)
        <li>
            <a href="service/check_wordforms_by_rules?search_lang={{$l_id}}">{{$l_name}}</a></li>
    @endforeach
    </ul>
    @endif
    
    <h3>Исправить данные</h3>
    @if (User::checkAccess('dict.edit'))
    <p><a href="service/copy_lemmas">Скопировать леммы из одного языка (наречия) в другой</a></p>
    @endif
    @if (User::checkAccess('admin'))
    <p><a href="service/add_wordform_affixes">Добавить аффиксы словоформам</a> (с грамсетами и неаналитические формы (без пробелов))</p>
    <p><a href="service/add_meaning_text_links">Добавить связи текст-значения</a></p>
    <p><a href="service/add_text_wordform_links">Добавить связи текст-словоформы</a></p>
    <p><a href="service/generate_wordforms">Сгенерировать словоформы по имеющимся</a></p>
    <p><a href="service/calculate_lemma_wordforms">Записать у лемм количество словоформ</a> ({{$count_lemmas_without_wordform_total}})</p>
    <p><a href="service/check_meaning_text">Исправить связи слово-значение (meaning_text)</a> 
       (исправить случаи, если есть проверенные и непроверенные примеры у одного слова, последним поставить метку "совсем не подходит")</p>
    <p><a href="service/add_accusatives">Добавить аккузативы для собственно карельских именных частей речи</a> (обходим все именные части речи, если есть номинативы и генитивы, создаем в этих диалектах соответствующие аккузативы)</p>
    <p><a href="service/create_initial_wordforms">Создать минимальный набор словоформ</a>. Если у изменяемой леммы нет основ, создаем основу 0 и генерируем леммы.</p>
    <p><a href="service/check_parallel_texts">Проверить параллельные тексты</a>. Вывести ссылки на тексты с параллельным русским, если не совпадает количество предложений.</p>
    <p><a href="service/tmp_fill_wordform_for_search">Заполнить wordform for search у таблицы связей "лемма"-"словоформа"</a>.</p>
    <p><a href="service/tmp_fill_genres">Добавить жанры для вепсских сказок</a>.</p>
    @endif
    
@endsection