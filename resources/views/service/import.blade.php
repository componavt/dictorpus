@extends('layouts.page')

@section('page_title')
Импорт
@endsection

@section('body')
        <p><a href="/import/concept_parser">Импорт понятий из СОСД</a></p>
        <p><a href="/import/concept_parser_check">Проверка понятий из СОС</a></p>
        <p><a href="/import/dict_parser?search_lang=1">Парсер вепсского словаря</a>
        <p><a href="/import/dict_zaikov_verb_parser">Парсер глаголов из словаря Зайкова</a>
        <p><a href="/import/wiki_photo">Импорт картинок с Викисклада для понятий</a>
@endsection
