@extends('layouts.page')

@section('page_title')
{{ trans('navigation.pos_common_wordforms') }}
@stop

@section('body')     
Пока ненужное представление
<p><b>{{trans('dict.lang')}}:</b> {{$search_lang}}</p>
@stop

