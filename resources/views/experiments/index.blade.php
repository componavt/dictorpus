@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <p><a href="/experiments/search_by_analog">Поиск по аналогии</a></p>
    <p><a href="/experiments/vowel_gradation">Поиск закономерностей в чередовании гласных имен</a></p>
    <p><a href="/experiments/vowel_gradation/verb_imp_3sg">Поиск закономерностей в чередовании гласных глаголов</a></p>
    <p><a href="/experiments/prediction_by_analog">Предсказание леммы и грамсета по аналогии</a></p>
    <p><a href="/experiments/pattern_search">Поиск закономерностей в формировании словоформ</a></p>

    <h4>Поиск закономерностей в анализе словоформ</h4>
    для тверского словаря
    <ul>    
        <li><a href="/experiments/pattern_search_in_wordforms?dialect_code=krl-new-tvr">сформировать множество</a></li>
        <li><a href="/experiments/pattern_search_in_wordforms_results?dialect_code=krl-new-tvr">посмотреть результаты</a></li>
    </ul>
    для ливвиковского словаря
    <ul>    
        <li><a href="/experiments/pattern_search_in_wordforms?dialect_code=olo-new">сформировать множество</a></li>
        <li><a href="/experiments/pattern_search_in_wordforms_results?dialect_code=olo-new">посмотреть результаты</a></li>
    </ul>

    <p><a href="/experiments/bible_language">Анализ языковых конструкций библейских текстов</a></p>

    <h4><a href="/experiments/dialect_dmarker">Определение диалектной принадлежности</a></h4>
@endsection