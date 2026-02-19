@extends('layouts.page')

@section('page_title')
Экспорт
@endsection

@section('body')
        <p><a href="export/unimorph">Экспорт словоформ в Unimorph</a>    </p>
        <ul>
        @foreach ($langs as $l_id=>$l_name)
            <li><a href="export/unimorph?search_lang={{$l_id}}">{{$l_name}}</a></li>
        @endforeach
        </ul>

        <p><a href="export/compounds_for_unimorph">Экспорт фразеологизмов в Unimorph</a></p>
        <p><a href="export/bible">Экспорт вепсской Библии</a></p>
        <p><a href="export/for_mobile">Экспорт данных для мобильной версии</a></p>
        <p><a href="export/for_speech">Экспорт ливвиковских текстов и словоформ для распознавания речи</a></p>
        <p><a href="export/multidict">Экспорт лемм мультисловаря</a></p>
        <p><a href="export/concepts">Экспорт всех понятий</a></p>
        <p><a href="export/concepts_without_images">Экспорт понятий без картинок</a></p>
        <p><a href="export/multidict_without_concepts">Экспорт лемм мультисловаря без понятий</a></p>
        <p><a href="export/olo_dict">Экспорт ливвиковского словаря</a></p>
        <p><a href="export/for_yandex">Экспорт предложений для Яндекса</a></p>
@endsection