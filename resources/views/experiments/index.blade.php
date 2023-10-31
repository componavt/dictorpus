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

    <h4>Определение диалектной принадлежности</h4>
    <ul>
        <li><a href="/experiments/dialect_dmarker">таблица с частотами</a></li>
        <li><a href="/experiments/dialect_dmarker/fractions">таблица с относительными частотами</a></li>
        <!--li><a href="/experiments/dialect_dmarker/words">посмотреть слова</a></li-->
        <li><a href="/experiments/dialect_dmarker/compare_freq_SSindex">сравнить относительные частоты и индекс Шепли-Шубика</a></li>
    @if (User::checkAccess('admin'))
        <li><a href="/experiments/dialect_dmarker/calculate">переписать частоты</a></li>
        <li><a href="/experiments/dialect_dmarker/calculate_coalitions">переcчитать коалиции</a></li>
        <li><a href="/experiments/dialect_dmarker/calculate_SSindex">переcчитать индексы Шепли-Шубика</a></li>
    @endif
    </ul>
@endsection