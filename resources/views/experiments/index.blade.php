@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h3>Поиск части речи по аналогии среди лемм и словоформ</h3>
    <p><a href="experiments/fill_search_pos">Сформировать множество лемм и словоформ для поиска части речи</a></p>
    <p><a href="experiments/evaluate_search_table?property=pos">Вычислить долю правильных ответов</a></p>
    <p><a href="experiments/results_search?property=pos">Вывод результатов</a></p>

    <h3>Поиск грамсетов по множеству словоформ</h3>
    <p><a href="experiments/fill_search_gramset">Сформировать множество словоформ для поиска грамсета</a></p>
    <p><a href="experiments/evaluate_search_table?property=gramset">Вычислить долю правильных ответов при поиске по конечным буквосочетаниям</a></p>
    <p><a href="experiments/evaluate_search_gramset_by_affix">Вычислить долю правильных ответов при поиске по псевдоокончаниям</a></p>
    <p><a href="experiments/results_search_gramset">Вывод результатов</a></p>
@endsection