@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    <h3>Исправить данные</h3>
    <p><a href="/service/add_wordform_affixes">Добавить аффиксы словоформам</a> (с грамсетами и неаналитические формы (без пробелов))</p>
    <ul>
    @foreach ($langs as $l_id=>$l_info)
        <li><a href="/service/add_wordform_affixes?search_lang={{$l_id}}">{{$l_info['name']}}</a> ({{$l_info['affix_count']}})</li>
    @endforeach
    </ul>
    
    <p>Заново вычислить стем и аффикс у леммы, обновить аффиксы у словоформ</a> (для лемм с ошибочными аффиксами словоформ (#))</p>
    <ul>
    @foreach ($langs as $l_id=>$l_info)
        <li>
        @if (!in_array($l_id, \App\Library\Grammatic::langsWithRules()))
            <a href="/service/reload_stem_affixes?search_lang={{$l_id}}">
        @endif
                {{$l_info['name']}}
        @if (!in_array($l_id, \App\Library\Grammatic::langsWithRules()))
            </a> 
        @endif
            ({{$l_info['wrong_affix_count']}})</li>
    @endforeach
    </ul>

    <p>Проверить в текстах неразмеченные слова и создать новые связи</p>
    <ul>
    @foreach ($langs as $l_id=>$l_info)
        <li>
            <a href="/service/add_unmarked_links?search_lang={{$l_id}}">
                {{$l_info['name']}}
            </a> 
            ({{$l_info['unmarked_words_count']}})</li>
    @endforeach
    </ul>

    <p>Проверить окончания у словоформ по флективным правилам</p>
    <ul>
    @foreach ($langs as $l_id=>$l_info)
        <li>
            <a href="/service/check_wordforms_by_rules?search_lang={{$l_id}}">
                {{$l_info['name']}}
            </a></li>
    @endforeach
    </ul>
    
@endsection