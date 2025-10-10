@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    @if (User::checkAccess('dict.edit'))
        <h3>Дополнение данных</h3>
        <p><a href="service/copy_lemmas">Скопировать леммы из одного языка (наречия) в другой</a></p>
        
        <p>Отбор лемм для ливвиковского мультимедийного словаря LiPas</p>
        <ul>
        <li><a href="service/dict/multi">Просмотреть отобранные леммы</a></li>
        <li><a href="service/dict/multi/select">Выбрать леммы</a></li>
        </ul>
        
        <!--p>Записать аудио</p>
        <ul>
            @foreach($lists as $l =>$n)
        <li><a href="service/audio/{{$l}}">{{$n}}</a></li>
            @endforeach
        </u-->
        
        <!--p>Отбор лемм для ливвиковского ШКОЛЬНОГО словаря</p>
        <ul>
        <li><a href="service/dict/school">Просмотреть отобранные леммы</a></li>
        <li><a href="service/dict/school/select">Выбрать леммы</a></li>
        </ul-->        
        
        <p>Отбор лемм для словаря Зайкова</p>
        <ul>
        <li><a href="service/dict/zaikov">Просмотреть отобранные леммы</a></li>
        <li><a href="service/dict/zaikov/select">Выбрать леммы</a></li>
        </ul>        
        
        <p>Отбор лемм для Людиковского диалектного лексикона</p>
        <ul>
        <li><a href="service/dict/ldl">Просмотреть отобранные леммы</a></li>
        <li><a href="service/dict/ldl/select">Выбрать леммы</a></li>
        </ul>       
        
        <p><a href="service/dict/synsets">Словарь синонимов</a></p>
        
    @endif

    <h3>Проверка</h3>
    <p>окончаний у словоформ по флективным правилам</p>
    <ul>
    @foreach ($langs as $l_id=>$l_name)
        <li>
            <a href="service/check_wordforms_by_rules?search_lang={{$l_id}}">{{$l_name}}</a></li>
    @endforeach
    </ul>
    <p><a href="service/check_author">Авторы публицистических текстов в источнике, а не в справочнике</a></p>
    @if (User::checkAccess('dict.edit'))
        <p><a href="service/lemmas_without_wordforms">Изменяемые леммы без словоформ</a></p>
    @endif
        
    @if (User::checkAccess('admin'))
    <a href="service/import"><h3>Импорт</h3></a>
    <a href="service/export"><H3>Экспорт</h3></a>
    <a href="service/correct"><H3>Исправление данных</h3></a>
    @endif    
@endsection