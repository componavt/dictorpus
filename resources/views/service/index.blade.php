@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    <h3>Проверка</h3>
    <p>окончаний у словоформ по флективным правилам</p>
    <ul>
    @foreach ($langs as $l_id=>$l_name)
        <li>
            <a href="service/check_wordforms_by_rules?search_lang={{$l_id}}">{{$l_name}}</a></li>
    @endforeach
    </ul>
    <p><a href="service/check_author">Авторы публицистических текстов в источнике, а не в справочнике</a>.</p>
    
    @if (User::checkAccess('dict.edit'))
        <h3>Дополнение данных</h3>
        <p><a href="service/copy_lemmas">Скопировать леммы из одного языка (наречия) в другой</a></p>
        <p>Отбор лемм для ливвиковского мультимедийного словаря</p>
        <ul>
        <li><a href="service/dict/multi">Просмотреть отобранные леммы</a></li>
        <li><a href="service/dict/multi/select">Выбрать леммы</a></li>
        </ul>
        
        <p>Отбор лемм для ливвиковского школьного словаря</p>
        <ul>
        <li><a href="service/dict/school">Просмотреть отобранные леммы</a></li>
        <li><a href="service/dict/school/select">Выбрать леммы</a></li>
        </ul>
    @endif
    
    @if (User::checkAccess('admin'))
    <a href="service/import"><h3>Импорт</h3></a>
    <a href="service/export"><H3>Экспорт</h3></a>
    <a href="service/correct"><H3>Исправление данных</h3></a>
    @endif    
@endsection