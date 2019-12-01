@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    <p><a href="export/unimorph">Экспорт словоформ в Unimorph</a>
        <ul>
        @foreach ([1, 4, 5, 6] as $l_id)
            <li><a href="export/unimorph?search_lang={{$l_id}}">{{\App\Models\Dict\Lang::getNameById($l_id)}}</a></li>
        @endforeach
        </ul>
    </p>
    <p><a href="export/compounds_for_unimorph">Экспорт фразеологизмов в Unimorph</a></p>
@endsection