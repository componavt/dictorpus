@extends('layouts.page')

@section('page_title')
{{ trans('navigation.omonyms') }}
@stop

@section('headExtra')
    {!!Html::style('css/history.css')!!}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}">{{ trans('messages.back_to_show') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}">{{ trans('messages.back_to_list') }}</a>
        </p>

        <h2>{{ $lemma->lemma }}</h2>
        @include('widgets.history._history', ['all_history' => $lemma->allHistory()])
@stop        
{{--
    /dict/lemma/652/history - reflexive verb
    /dict/lemma/3458/history - reflexive verb
    /dict/lemma/2880/history    
    /dict/lemma/3598/history
    /dict/lemma/56/history
    /dict/lemma/1386/history
    --}}
