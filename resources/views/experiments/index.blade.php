@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<p><a href="/experiments/search_by_analog">Поиск по аналогии</a></p>
<p><a href="/experiments/vowel_gradation">Поиск закономерностей в чередовании гласных имен</a></p>
<p><a href="/experiments/vowel_gradation/verb_imp_3sg">Поиск закономерностей в чередовании гласных глаголов</a></p>
<p><a href="/experiments/prediction_by_analog">Предсказание леммы и грамсета по аналогии</a></p>
@endsection