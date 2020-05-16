@extends('layouts.page')

@section('page_title')
    Вывод ошибок
@stop

@section('headExtra')
    <link media="all" type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css">
    {!!Html::style('css/table.css')!!}
@stop

@section('body')     
    <h2>Поиск {{trans('dict.of_'.$property)}}
    @if($property == 'gramset')
        @if($type == 'aff')
        по псевдоокончаниям
        @else
        по конечным буквосочетаниям
        @endif
    @endif
    </h2>
    <h3>Случаи, когда среди предсказанных ответов нет ни одного правильного</h3>
    <p><b>Язык:</b> {{$search_lang_name}}</p>
    <p>Всего: {{sizeof($wordforms)}}</p>
    
    <table id="errorTable" class="table table-striped rwd-table wide-md">
        <tr>
            <th>Словоформа</th>
            <th>{{trans('dict.'.$property)}}</th>
            <th>Предсказано</th>
            @if($property == 'pos')
            <th>Проверить</th>
            @endif
        </tr>
        @foreach ($wordforms as $wordform)
        <tr>
            <td><a href="/dict/wordform?search_wordform={{$wordform->wordform}}&search_lang={{$search_lang}}">
                {!!\App\Library\Str::highlightTail($wordform->wordform, $wordform->tale, '<span style="color:red">', '</span>')!!}
                </a>
            </td>
            @if($property == 'pos')
            <td>{{\App\Models\Dict\PartOfSpeech::getNameById($wordform->prop)}}</td>
            <td>{{\App\Models\Dict\PartOfSpeech::getNameById($wordform->winner)}}</td>
            <td><a href="/dict/lemma?search_lemma={{$wordform->wordform}}&search_lang={{$search_lang}}">
                лемму
                </a>
            </td>
            @elseif($property == 'gramset')
            <td>{{\App\Models\Dict\Gramset::getStringByID($wordform->prop)}}</td>
            <td>{{\App\Models\Dict\Gramset::getStringByID($wordform->winner)}}</td>
            @endif
        </tr>
        @endforeach
    </table>
@stop

@section('footScriptExtra')
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
@stop

@section('jqueryFunc')
    $(document).ready( function () {
        $('#errorTable').DataTable();
    } );
@stop



