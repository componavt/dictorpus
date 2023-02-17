@extends('layouts.page')

@section('page_title')
{{ trans('navigation.informants') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
    @include('corpus.informant._about')
    
    <h3>{{trans('dict.dict_sound')}}</h3>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) }}">Просмотреть озвученные слова</a> ({{$informant->audios()->count()}})</p>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) .'/edit'}}">Редактировать список для озвучивания</a> ({{$informant->lemmas()->count()}})</p>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) .'/add'}}">Добавить в список новые слова для озвучивания</a> ({{$unvoiced_lemmas_count}})</p>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id. '/record') }}">Озвучить новые слова</a> ({{$informant->lemmas()->count()}})</p>
@stop



