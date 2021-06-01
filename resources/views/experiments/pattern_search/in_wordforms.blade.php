<?php $list_count=1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Поиск закономерностей в анализе словоформ</h2>
    <p><i>{{$lang}}, {{$dialect}}</i></p>
    
    Метология:
    <ol>
        <li>В цикле по алфавиту перебираются различные окончания.</li>    
        <li>Сначала рассматриваются слова, оканчивающиеся на <b>-a</b>.</li> 
        <li>Выбираются все пары "часть речи"-"грамсет", соответствующие найденным словам.</li>
        <li>Если таких слов не нашлось, то берется следующая буква (проверяются слова на <b>-b</b>).</li>
        <li>Если нашлось больше одной буквы, то все шаги повторяются со словами на <b>-aa</b>, <b>-ba</b> и т.д.</li>
        <li>Если найдена только одна пара "часть речи"-"грамсет" или длина искомого окончания больше 5 символов, то поиск останавливается.</li>
    </ol>
    
    <p>Из эксперимента были исключены аналитические словоформы и грамсеты, имеющие идентичные словоформы с другими грамсетами:</p>
    <ul>
    @foreach ($dubles as $g1 => $g2)
    <li>{{\App\Models\Dict\Gramset::getStringById($g2)}} (={{\App\Models\Dict\Gramset::getStringById($g1)}})</li>
    @endforeach
    </ul>
    
    <table class="table-bordered table-wide rwd-table wide-lg">
    @foreach ($patterns as $ending => $gramsets) 
    <tr>
        <td style="vertical-align:top" rowspan="{{sizeof($gramsets)}}"><b>-{{$ending}}</b></td>
        @for($i=0; $i<sizeof($gramsets); $i++)
            @if($i>0)
    </tr>
    <tr>
            @endif
        <td>{{\App\Models\Dict\PartOfSpeech::getNameById($gramsets[$i]['pos_id'])}}</td>
        <td>{{\App\Models\Dict\Gramset::getStringById($gramsets[$i]['gramset_id'])}}</td>
        <td><a href="/ru/dict/wordform?search_dialect={{$dialect_id}}&search_gramset={{$gramsets[$i]['gramset_id']}}&search_lang={{$lang_id}}&search_pos={{$gramsets[$i]['pos_id']}}&search_wordform=%25{{$ending}}">{{$gramsets[$i]['count']}}</a></td>
        @endfor
    </tr>
    @endforeach
    </table>
@endsection