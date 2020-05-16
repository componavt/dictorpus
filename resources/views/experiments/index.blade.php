@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<p><a href="/experiments/search_by_analog">Поиск по аналогии</a></p>
<p><a href="/experiments/vowel_gradation">Поиск закономерностей в чередовании гласных</a></p>
@endsection