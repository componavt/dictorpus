@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Анализ языковых конструкций библейских текстов</h2>
    <ol>
    <li><a href="/experiments/bible_language/for_selection/2">для отобранных библейских текстов</a></li>
    <li><a href="/experiments/bible_language/for_selection/3">для отобранных публицистические текстов</a></li>
    <li><a href="/experiments/bible_language/for_selection/8">для отобранных художественные текстов</a></li>
    <li><a href="/experiments/bible_language/for_all?for_selection=1">для всех отобранных текстов</a></li>
    <li><a href="/experiments/bible_language/for_all?lang_id=4">для всех собственно карельских текстов</a></li>
    <li><a href="/experiments/bible_language/for_all">для всех текстов</a></li>
    </ol>
    
@endsection