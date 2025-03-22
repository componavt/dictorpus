@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Генерация словоформ для людиковского наречия карельского языка на примере Святозерского диалекта</h2>
    
    @foreach (['names' => 'Имена', 'verbs' => 'Глаголы'] as $what => $title)
    
    <h3>{{ $title }}</h3>
    
    <ul>
        <li><a href="{{ route('ludgen.words', ['what'=>$what]) }}">Отобранные слова</a></li>
        <li><a href="{{ route('ludgen.bases', ['what'=>$what]) }}">Основы</a></li>
        <li><a href="{{ route('ludgen.affixes', ['what'=>$what]) }}">Окончания{{ $what == 'verbs' ? ', вспомогательные глаголы' : '' }}</a></li>
    </ul>
    
    @endforeach
    
    <li><a href="{{ route('ludgen.verb_types') }}">Виды глаголов</a></li>
@endsection

@section('footScriptExtra')
    {!!Html::script('js/text.js')!!}
    {!!Html::script('js/form.js')!!}
@endsection

@section('jqueryFunc')
    limitTextarea("#text");
@endsection