@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
<h2>Добавить аффиксы словоформам</h2>
<h3>Проверяются словоформы с грамсетами, но неаналитические формы (без пробелов).</h3>
<p>Сначала выбираются все леммы, у которых есть словоформы без аффиксов. 
    Извлекаются стем и аффикс у леммы (если нужно, предварительно вычисляются и записываются), 
    потом выбираются словоформы без аффиксов, вычисляются аффиксы словоформ и записываются.
</p>
<p>Количество словоформ (с грамсетами и без пробелов) без аффиксов:</p>
    <ul>
    @foreach ($langs as $l_id=>$l_info)
        <li><a href="/service/add_wordform_affixes?search_lang={{$l_id}}">{{$l_info['name']}}</a> ({{$l_info['affix_count']}})</li>
    @endforeach
    </ul>
    
<h3>Заново вычислить стем и аффикс у леммы, обновить аффиксы у словоформ</h3> 
<p>Выбрать лемм с ошибочными аффиксами словоформ (#). Заново вычислить стем и аффикс леммы по её словоформам. Заново вычислить аффиксы у всех словоформ леммы.</p>

<p>Количество лемм с ошибочными аффиксами словоформ (#):</p>
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


@endsection