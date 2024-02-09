@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Генерация словоформ для людиковского наречия карельского языка на примере Святозерского диалекта</h2>
    
    <ul>
        <li><a href="{{ route('ludgen.words', ['what'=>'names']) }}">Имена</a></li>
        <li><a href="{{ route('ludgen.words', ['what'=>'verbs']) }}">Глаголы</a></li>
    </ul>
    
@endsection

@section('footScriptExtra')
    {!!Html::script('js/text.js')!!}
    {!!Html::script('js/form.js')!!}
@endsection

@section('jqueryFunc')
    limitTextarea("#text");
@endsection