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
        <p><a href="export/for_speech">Экспорт данных для распознавания речи</a></p>
        <p><a href="export/multidict">Экспорт лемм мультисловаря</a></p>
        <p><a href="export/conceps_without_images">Экспорт понятий без картинок</a></p>
        <p><a href="export/multidict_without_concepts">Экспорт лемм мультисловаря без понятий</a></p>
@endsection