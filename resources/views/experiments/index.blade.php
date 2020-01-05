@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h3>Поиск части речи и грамсетов по аналогии среди уникальных словоформ</h3>
    <p><a href="experiments/fill_unique_wordforms">Записать множество уникальных словоформ</a></p>
    <p><a href="experiments/search_pos_gramsets_by_unique_wordforms">Вычислить долю правильных ответов</a></p>
    <p><a href="experiments/search_pos_gramsets_by_unique_wordforms_results">Оценка результатов</a></p>
    
    <h3>Поиск части речи и грамсетов по псевдоокончаниям</h3>
    <p><a href="experiments/search_pos_gramsets_by_affix">Вычислить долю правильных ответов</a></p>
@endsection