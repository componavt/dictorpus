@extends('layouts.ldl')

@section('body')   
<h1>Статистика на {{ date('d-m-Y') }}</h1>
<p>{{ format_number($concepts_c) }} понятий</p>
<p>{{ format_number($lemmas_c) }} лемм</p>

<div style="margin-left: 20px;">
@foreach ($poses as $pos_name => $count)
    <p>{{ $pos_name }}: {{ $count }}</p>
@endforeach
</div>
<p>{{ format_number($meanings_c) }} значений</p>
<p>{{ format_number($sentences_c) }} примеров</p>
<p>{{ format_number($wordforms_c) }} словоформ</p>

{{--<p>Общее количество символов: {{ format_number($symbols) }}</p>
<p>Авторских листов: {{ round($symbols/40000) }} </p>--}}
@stop


